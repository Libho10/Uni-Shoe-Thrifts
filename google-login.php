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
    sendJson(false, "Google login token is missing.");
}

$client = new Google_Client([
    "client_id" => "694501172256-5lgva3adqcs6df3ho75907qim3ibi4vu.apps.googleusercontent.com"
]);

$payload = $client->verifyIdToken($idToken);

if (!$payload) {
    sendJson(false, "Invalid Google login token.");
}

$email = $payload["email"] ?? "";
$emailVerified = $payload["email_verified"] ?? false;

if ($email === "" || !$emailVerified) {
    sendJson(false, "Google did not provide a verified email address.");
}

$checkUser = $conn->prepare(
    "SELECT user_id, first_name, last_name FROM users WHERE email = ?"
);

$checkUser->bind_param("s", $email);
$checkUser->execute();
$checkUser->store_result();

if ($checkUser->num_rows === 0) {
    $checkUser->close();
    $conn->close();

    sendJson(false, "No account exists with this Google email. Please sign up first.");
}

$checkUser->bind_result($userId, $firstName, $lastName);
$checkUser->fetch();
$checkUser->close();

session_start();

$_SESSION["user_id"] = $userId;
$_SESSION["email"] = $email;
$_SESSION["first_name"] = $firstName;
$_SESSION["last_name"] = $lastName;
$_SESSION["logged_in"] = true;

logAudit($conn, $userId, "GOOGLE_LOGIN_SUCCESS");

$conn->close();

sendJson(true, "Welcome back, " . $firstName . "!");

$_SESSION["user_id"] = $user["user_id"];
$_SESSION["welcome_message"] = "Welcome back, " . $user["first_name"] . "!";

header("Location: index.php");
exit();