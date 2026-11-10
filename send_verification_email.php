<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once __DIR__ . "/vendor/autoload.php";

function sendVerificationEmail($recipientEmail, $recipientName, $verificationToken) {

    $verificationLink =
        "http://localhost/UTH-website/verify_email.php?token=" .
        urlencode($verificationToken);

    $mail = new PHPMailer(true);

    try {
        $mail->isSMTP();
        $mail->Host = "smtp.gmail.com";
        $mail->SMTPAuth = true;
        $mail->Username = "yourgmailaddress@gmail.com";
        $mail->Password = "YOUR_16_CHARACTER_APP_PASSWORD";
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        $mail->setFrom("yourgmailaddress@gmail.com", "UTH Website");
        $mail->addAddress($recipientEmail, $recipientName);

        $mail->isHTML(true);
        $mail->Subject = "Verify Your UTH Website Account";

        $mail->Body = "
            <h2>Welcome to UTH Website</h2>
            <p>Hi {$recipientName},</p>
            <p>Please click the button below to verify your email address:</p>
            <p>
                <a href='{$verificationLink}'
                    style='
                        display:inline-block;
                        padding:12px 24px;
                        background-color:#0d6efd;
                        color:white;
                        text-decoration:none;
                        border-radius:5px;
                    '>
                    Verify Email Address
                </a>
            </p>
            <p>This link expires in 24 hours.</p>
            <p>If you did not create an account, please ignore this email.</p>
        ";

        $mail->AltBody = "
            Welcome to UTH Website.

            Please verify your email address by opening this link:
            {$verificationLink}

            This link expires in 24 hours.
        ";

        $mail->send();

        return true;

    } catch (Exception $error) {
        error_log("Email error: " . $mail->ErrorInfo);
        return false;
    }
}