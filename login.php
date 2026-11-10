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

$email = trim($_POST["email"] ?? "");
$password = trim($_POST["password"] ?? "");

if ($email === "" || $password === "") {
    sendJson(false, "Please complete all fields.");
}

$checkUser = $conn->prepare(
    "SELECT
        user_id,
        first_name,
        last_name,
        email,
        password_hash,
        status
    FROM users
    WHERE email = ?
    LIMIT 1"
);

if (!$checkUser) {
    sendJson(false, "Could not prepare the login query.");
}

$checkUser->bind_param("s", $email);
$checkUser->execute();

$userId = null;
$firstName = null;
$lastName = null;
$emailFromDatabase = null;
$passwordHash = null;
$status = null;

$checkUser->bind_result(
    $userId,
    $firstName,
    $lastName,
    $emailFromDatabase,
    $passwordHash,
    $status
);

$checkUser->fetch();
$checkUser->close();

if ($userId === null) {
    logAudit($conn, null, "USER_LOGIN_FAILED");
    $conn->close();

    sendJson(false, "Invalid email address or password.");
}
$passwordHash = (string) $passwordHash; 

if (!password_verify($password, $passwordHash)) {
    logAudit($conn, $userId, "USER_LOGIN_FAILED");
    $conn->close();

    sendJson(false, "Invalid email address or password.");
}

if ($status !== "active") {
    logAudit($conn, $userId, "USER_LOGIN_BLOCKED_INACTIVE");
    $conn->close();

    sendJson(false, "This account is not active.");
}

$_SESSION["user_id"] = $userId;
$_SESSION["email"] = $emailFromDatabase;
$_SESSION["first_name"] = $firstName;
$_SESSION["last_name"] = $lastName;
$_SESSION["logged_in"] = true;

logAudit($conn, $userId, "USER_LOGIN_SUCCESS");
$conn->close();

sendJson(true, "Welcome back, " . $firstName . "!");