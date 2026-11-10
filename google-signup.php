<?php
header("Content-Type: application/json; charset=UTF-8");

require_once "connect.php";

require_once "vendor/autoload.php";

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

$input = json_decode(file_get_contents("php://input"), true);

$idToken = $input["id_token"] ?? "";

if ($idToken === "") {
    sendJson(false, "Google sign-up token is missing.");
}

$client = new Google_Client([
    "client_id" => "694501172256-5lgva3adqcs6df3ho75907qim3ibi4vu.apps.googleusercontent.com"
]);

$payload = $client->verifyIdToken($idToken);

if (!$payload) {
    sendJson(false, "Invalid Google sign-up token.");
}

$googleId = $payload["sub"];
$email = $payload["email"] ?? "";
$firstName = $payload["given_name"] ?? "";
$lastName = $payload["family_name"] ?? "";
$emailVerified = $payload["email_verified"] ?? false;

if ($email === "" || !$emailVerified) {
    sendJson(false, "Google did not provide a verified email address.");
}

$checkEmail = $conn->prepare(
    "SELECT user_id FROM users WHERE email = ?"
);

$checkEmail->bind_param("s", $email);
$checkEmail->execute();
$checkEmail->store_result();

if ($checkEmail->num_rows > 0) {
    $checkEmail->close();
    $conn->close();

    sendJson(false, "An account with this Google email already exists. Please log in instead.");
}

$status = "active";
$termsAccepted = 1;

// Google users do not use a password.
// Store an unusable random value in password_hash.
$passwordHash = password_hash(bin2hex(random_bytes(32)), PASSWORD_DEFAULT);

$insertUser = $conn->prepare(
    "INSERT INTO users
    (first_name, last_name, email, password_hash, terms_accepted, status)
    VALUES (?, ?, ?, ?, ?, ?)"
);

$insertUser->bind_param(
    "ssssis",
    $firstName,
    $lastName,
    $email,
    $passwordHash,
    $termsAccepted,
    $status
);

if (!$insertUser->execute()) {
    $insertError = $insertUser->error;

    $checkEmail->close();
    $insertUser->close();
    $conn->close();

    sendJson(false, "Could not create your Google account: " . $insertError);
}
$newUserId = $conn->insert_id;

$termsVersion = "v1.0";

$insertConsent = $conn->prepare(
    "INSERT INTO UserConsent
    (user_id, terms_version)
    VALUES (?, ?)"
);

$insertConsent->bind_param("is", $newUserId, $termsVersion);
$insertConsent->execute();

$insertConsent->close();

logAudit($conn, $newUserId, "GOOGLE_SIGNUP");

$checkEmail->close();
$insertUser->close();
$conn->close();

sendJson(true, "Your Google account was created successfully.");