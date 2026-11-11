<?php
session_start();
require_once "connect.php";

$token = $_GET["token"] ?? "";

if ($token === "") {
    die("Invalid verification link.");
}

$verifyUser = $conn->prepare(
    "SELECT user_id, verification_expires_at
    FROM users
    WHERE verification_token = ?
    AND email_verified = 0
    LIMIT 1"
);

if (!$verifyUser) {
    die("Could not prepare the verification query.");
}

$verifyUser->bind_param("s", $token);
$verifyUser->execute();

$userId = null;
$expiresAt = null;

$verifyUser->bind_result($userId, $expiresAt);
$verifyUser->fetch();
$verifyUser->close();

if ($userId === null) {
    die("This verification link is invalid or has already been used.");
}

$expiresAt = (string) $expiresAt; 
if (strtotime($expiresAt) < time()) {
    die("This verification link has expired. Please request a new verification email.");
}

$updateUser = $conn->prepare(
    "UPDATE users
    SET
        email_verified = 1,
        verification_token = NULL,
        verification_expires_at = NULL
    WHERE user_id = ?"
);

$updateUser->bind_param("i", $userId);
$updateUser->execute();
$updateUser->close();

$conn->close();

echo "
    <h1>Email Verified</h1>
    <p>Your email address has been verified successfully.</p>
    <a href='index.php'>Go to Homepage</a>
";