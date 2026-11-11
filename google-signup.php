<?php
session_start();
header("Content-Type: application/json; charset=UTF-8");

require_once "connect.php";
require_once "log_audit.php";

function sendJson($success, $message) {
    echo json_encode([
        "success" => $success,
        "message" => $message
    ]);
    exit();
}

function debugError($message) {
    error_log($message);
    sendJson(false, "Google sign-up failed. Please try again.");
}

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    sendJson(false, "Only POST requests are allowed.");
}

$input = file_get_contents("php://input");
$data = json_decode($input, true);

if (!$data || empty($data["credential"])) {
    debugError("Google credential missing.");
}

$credential = $data["credential"];

// Decode the Google JWT token
$tokenParts = explode(".", $credential);

if (count($tokenParts) !== 3) {
    debugError("Invalid Google credential format.");
}

$payload = json_decode(base64_decode(str_replace("-", "+", str_replace("_", "/", $tokenParts[1]))), true);

if (!$payload) {
    debugError("Could not decode Google token.");
}

$googleEmail = $payload["email"] ?? "";
$firstName = $payload["given_name"] ?? "";
$lastName = $payload["family_name"] ?? "";
$googleId = $payload["sub"] ?? "";

if ($googleEmail === "") {
    debugError("Google email address is missing.");
}

// Check whether the user already exists
$checkUser = $conn->prepare(
    "SELECT user_id, first_name, last_name, email
    FROM users
    WHERE email = ?
    LIMIT 1"
);

if (!$checkUser) {
    debugError("Could not prepare user check query.");
}

$checkUser->bind_param("s", $googleEmail);
$checkUser->execute();

$userId = null;
$existingFirstName = null;
$existingLastName = null;
$emailFromDatabase = null;

$checkUser->bind_result(
    $userId,
    $existingFirstName,
    $existingLastName,
    $emailFromDatabase
);

$checkUser->fetch();
$checkUser->close();

// If the user already exists, log them in
if ($userId !== null) {

    $_SESSION["logged_in"] = true;
    $_SESSION["user_id"] = $userId;
    $_SESSION["email"] = $emailFromDatabase;
    $_SESSION["first_name"] = $existingFirstName;
    $_SESSION["last_name"] = $existingLastName;

    logAudit($conn, $userId, "USER_LOGIN_SUCCESS");
    $conn->close();

    sendJson(true, "Welcome back, " . $existingFirstName . "!");
}

// Create a new Google user
$status = "active";
$termsAccepted = 1;
$passwordHash = password_hash(bin2hex(random_bytes(16)), PASSWORD_DEFAULT);

$insertUser = $conn->prepare(
    "INSERT INTO users (
        first_name,
        last_name,
        email,
        password_hash,
        terms_accepted,
        status,
        created_at
    )
    VALUES (?, ?, ?, ?, ?, ?, NOW())"
);

if (!$insertUser) {
    debugError("Could not prepare Google user insert query.");
}

$insertUser->bind_param(
    "ssssis",
    $firstName,
    $lastName,
    $googleEmail,
    $passwordHash,
    $termsAccepted,
    $status
);

$insertUser->execute();

$userId = $conn->insert_id;
$insertUser->close();

// Creating the email verification token
$verificationToken = bin2hex(random_bytes(32));
$verificationExpiresAt = date("Y-m-d H:i:s", strtotime("+24 hours"));

$updateVerification = $conn->prepare(
    "UPDATE users
    SET
        verification_token = ?,
        verification_expires_at = ?
    WHERE user_id = ?"
);

if (!$updateVerification) {
    debugError("Could not prepare Google verification update query.");
}

$updateVerification->bind_param(
    "ssi",
    $verificationToken,
    $verificationExpiresAt,
    $userId
);

$updateVerification->execute();
$updateVerification->close();

// Send the verification email
require_once "send_verification_email.php";

$emailSent = sendVerificationEmail(
    $googleEmail,
    $firstName,
    $verificationToken
);

// Automatically log the user in after Google signup
$_SESSION["logged_in"] = true;
$_SESSION["user_id"] = $userId;
$_SESSION["email"] = $googleEmail;
$_SESSION["first_name"] = $firstName;
$_SESSION["last_name"] = $lastName;

logAudit($conn, $userId, "USER_SIGNUP");
$conn->close();

if ($emailSent) {
    sendJson(
        true,
        "Welcome to UTH Website, " . $firstName . "! Please check your email to verify your account."
    );
}
sendJson(
    true,
    "Welcome to UTH Website, " . $firstName . "! The verification email could not be sent."
);