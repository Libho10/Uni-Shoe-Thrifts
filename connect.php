<?php
$servername = "localhost";
$username = "root";
$password = "";
$db = "uthwebsite";

$conn = new mysqli($servername, $username, $password, $db);

if ($conn->connect_error) {
    header("Content-Type: application/json; charset=UTF-8");
    http_response_code(500);

    echo json_encode([
        "success" => false,
        "message" => "Database connection failed."
    ]);

    exit();
}

$conn->set_charset("utf8mb4");