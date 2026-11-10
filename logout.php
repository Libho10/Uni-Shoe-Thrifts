<?php
session_start();

// Clear all session data
$_SESSION = [];

// Destroy the server-side session
session_destroy();

// Expire the PHP session cookie
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();

    setcookie(
        session_name(),
        "",
        time() - 42000,
        $params["path"],
        $params["domain"],
        $params["secure"],
        $params["httponly"]
    );
}

// Sends the user back to the homepage
header("Location: index.php");
exit();