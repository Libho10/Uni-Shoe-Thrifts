<?php
session_start();
header("Content-Type: application/json");

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    echo json_encode(["success" => false, "message" => "Invalid request."]);
    exit();
}

$method = $_POST["fulfillment_method"] ?? "";

if ($method !== "collect" && $method !== "deliver") {
    echo json_encode(["success" => false, "message" => "Invalid fulfilment method."]);
    exit();
}

$_SESSION["fulfillment_method"] = $method;

echo json_encode(["success" => true]);