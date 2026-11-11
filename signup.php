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

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    sendJson(false, "Only POST requests are allowed.");
}

$firstName = trim($_POST["first_name"] ?? "");
$lastName = trim($_POST["last_name"] ?? "");
$email = trim($_POST["email"] ?? "");
$password = trim($_POST["password"] ?? "");

$termsAccepted = isset($_POST["terms_accepted"]) && $_POST["terms_accepted"] === "1";

if ($firstName === "" || $lastName === "" || $email === "" || $password === "") {
    sendJson(false, "Please complete all required fields.");
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    sendJson(false, "Please enter a valid email address.");
}

if (strlen($password) < 8) {
    sendJson(false, "Your password must be at least 8 characters long.");
}

if (!$termsAccepted) {
    sendJson(false, "You must accept the Terms and Conditions.");
}

$checkEmail = $conn->prepare(
    "SELECT user_id
    FROM users
    WHERE email = ?"
);

if (!$checkEmail) {
    sendJson(false, "Could not prepare the email check query.");
}

$checkEmail->bind_param("s", $email);
$checkEmail->execute();
$checkEmail->store_result();

if ($checkEmail->num_rows > 0) {
    $checkEmail->close();
    $conn->close();

    sendJson(false, "An account with that email address already exists.");
}

$passwordHash = password_hash($password, PASSWORD_DEFAULT);
$termsAcceptedValue = 1;
$status = "active";

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
    $checkEmail->close();
    $conn->close();

    sendJson(false, "Could not prepare the user insert query.");
}

$insertUser->bind_param(
    "ssssis",
    $firstName,
    $lastName,
    $email,
    $passwordHash,
    $termsAcceptedValue,
    $status
);

if (!$insertUser->execute()) {
    $insertError = $insertUser->error;

    $checkEmail->close();
    $insertUser->close();
    $conn->close();

    sendJson(false, "Database insert failed. Please try again.");
}

$newUserId = $conn->insert_id;

$insertUser->close();

$termsVersion = "v1.0";

$insertConsent = $conn->prepare(
    "INSERT INTO UserConsent (
        user_id,
        terms_version
    )
    VALUES (?, ?)"
);

if ($insertConsent) {
    $insertConsent->bind_param("is", $newUserId, $termsVersion);
    $insertConsent->execute();
    $insertConsent->close();
}

logAudit($conn, $newUserId, "USER_SIGNUP");

// Automatically log the user in after successful signup
$_SESSION["logged_in"] = true;
$_SESSION["user_id"] = $newUserId;
$_SESSION["email"] = $email;
$_SESSION["first_name"] = $firstName;
$_SESSION["last_name"] = $lastName;


// Create verification token
$verificationToken = bin2hex(random_bytes(32));
$verificationExpiresAt = date("Y-m-d H:i:s", strtotime("+24 hours"));

$updateVerification = $conn->prepare(
    "UPDATE users
    SET
        verification_token = ?,
        verification_expires_at = ?
    WHERE user_id = ?"
);

$updateVerification->bind_param(
    "ssi",
    $verificationToken,
    $verificationExpiresAt,
    $newUserId
);

$updateVerification->execute();
$updateVerification->close();

require_once "send_verification_email.php";

$emailSent = sendVerificationEmail(
    $email,
    $firstName,
    $verificationToken
);

$checkEmail->close();
$conn->close();

sendJson(true, "Your account was created successfully.");