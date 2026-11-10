<?php
session_start();
require_once "connect.php";

header("Content-Type: application/json");

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    echo json_encode(["success" => false, "message" => "Invalid request."]);
    exit();
}

$input = json_decode(file_get_contents("php://input"), true);
$paypalOrderId = $input["order_id"] ?? "";

if (empty($paypalOrderId) || empty($_SESSION["paypal_order_id"])) {
    echo json_encode(["success" => false, "message" => "Invalid PayPal order."]);
    exit();
}

if ($paypalOrderId !== $_SESSION["paypal_order_id"]) {
    echo json_encode(["success" => false, "message" => "PayPal order mismatch."]);
    exit();
}

$clientId = "mypaypal_SANDBOX_CLIENT_ID";
$clientSecret = "mypaypal_SANDBOX_CLIENT_SECRET";
$apiBase = "https:sandbox.paypal.com";

function getAccessToken($apiBase, $clientId, $clientSecret) {
    $ch = curl_init();

    curl_setopt($ch, CURLOPT_URL, $apiBase . "/v1/oauth2/token");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_USERPWD, $clientId . ":" . $clientSecret);
    curl_setopt($ch, CURLOPT_POSTFIELDS, "grant_type=client_credentials");

    $response = curl_exec($ch);
    curl_close($ch);

    $data = json_decode($response, true);

    return $data["access_token"] ?? null;
}

// This function will finalise and collect the payment for the order using the PayPay external API
function captureOrder($apiBase, $accessToken, $orderId) {
    $ch = curl_init(); //opens the channel to talk to an external server

    curl_setopt($ch, CURLOPT_URL, $apiBase . "/v2/checkout/orders/" . $orderId . "/capture");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "Content-Type: application/json",
        "Authorization: Bearer " . $accessToken
    ]);

    // will send the request across internet and save the servers reply 
    $response = curl_exec($ch);
    curl_close($ch);

    return json_decode($response, true);
}

$accessToken = getAccessToken($apiBase, $clientId, $clientSecret);

if (!$accessToken) {
    echo json_encode(["success" => false, "message" => "Could not connect to PayPal."]);
    exit();
}

$capture = captureOrder($apiBase, $accessToken, $paypalOrderId);

if (($capture["status"] ?? "") !== "COMPLETED") {
    echo json_encode([
        "success" => false,
        "message" => "PayPal payment was not completed."
    ]);
    exit();
}

// Clear the cart session after successful payment
unset($_SESSION["cart_id"]);
unset($_SESSION["paypal_order_id"]);
unset($_SESSION["checkout_address_id"]);

echo json_encode([
    "success" => true,
    "message" => "Payment completed."
]);