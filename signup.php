<?php
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
    "SELECT user_id FROM users WHERE email = ?"
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
    "INSERT INTO users
    (first_name, last_name, email, password_hash, terms_accepted, status)
    VALUES (?, ?, ?, ?, ?, ?)"
);

if (!$insertUser) {
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

    sendJson(false, "Database insert failed: " . $insertError);
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

logAudit($conn, $newUserId, "USER_SIGNUP");

$checkEmail->close();
$insertUser->close();
$conn->close();

sendJson(true, "Your account was created successfully.");