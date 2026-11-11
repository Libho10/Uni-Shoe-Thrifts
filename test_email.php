<?php

require_once __DIR__ . "/send_verification_email.php";

$sent = sendVerificationEmail(
    "wat3rmlon@gmail.com",
    "Test User",
    bin2hex(random_bytes(32))
);

header("Content-Type: text/plain; charset=UTF-8");

echo $sent
    ? "SMTP accepted the test email. Check your inbox and spam folder."
    : "Email failed. Check the PHP/Apache error log.";