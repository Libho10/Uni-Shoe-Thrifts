<?php
session_start();
require_once "connect.php";

header("Content-Type: application/json");

if (empty($_SESSION["logged_in"]) || empty($_SESSION["user_id"]) || empty($_SESSION["cart_id"])) {
    echo json_encode([
        "success" => false,
        "message" => "Please log in and add an item to your cart."
    ]);
    exit();
}

$clientId = "YOUR_PAYPAL_SANDBOX_CLIENT_ID";
$clientSecret = "YOUR_PAYPAL_SANDBOX_CLIENT_SECRET";

$apiBase = "https://api-m.sandbox.paypal.com";

function getPayPalAccessToken($apiBase, $clientId, $clientSecret) {
    $ch = curl_init();

    curl_setopt($ch, CURLOPT_URL, $apiBase . "/v1/oauth2/token");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_USERPWD, $clientId . ":" . $clientSecret);
    curl_setopt($ch, CURLOPT_POSTFIELDS, "grant_type=client_credentials");

    $response = curl_exec($ch);

    if (curl_errno($ch)) {
        curl_close($ch);
        return null;
    }

    curl_close($ch);

    $data = json_decode($response, true);

    return $data["access_token"] ?? null;
}

function createPayPalOrder($apiBase, $accessToken, $amount, $description) {
    $payload = json_encode([
        "intent" => "CAPTURE",
        "purchase_units" => [
            [
                "amount" => [
                    "currency_code" => "ZAR",
                    "value" => number_format($amount, 2, ".", "")
                ],
                "description" => $description
            ]
        ]
    ]);

    $ch = curl_init();

    curl_setopt($ch, CURLOPT_URL, $apiBase . "/v2/checkout/orders");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "Content-Type: application/json",
        "Authorization: Bearer " . $accessToken
    ]);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);

    $response = curl_exec($ch);

    if (curl_errno($ch)) {
        curl_close($ch);
        return null;
    }

    curl_close($ch);

    return json_decode($response, true);
}

// Recalculate the total on the server
$cartId = (int) $_SESSION["cart_id"];

$getTotal = $conn->prepare(
    "SELECT COALESCE(SUM(pv.price * ci.quantity), 0) AS item_total
    FROM cart_items ci
    INNER JOIN product_variants pv
        ON pv.product_variant_id = ci.product_variant_id
    WHERE ci.cart_id = ?
    AND ci.deleted_at IS NULL"
);

$getTotal->bind_param("i", $cartId);
$getTotal->execute();
$totalResult = $getTotal->get_result();
$totalRow = $totalResult->fetch_assoc();

$itemTotal = (float) $totalRow["item_total"];
$platformFee = 1.50;
$deliveryFee = 23.50;

$fulfillmentMethod = $_SESSION["fulfillment_method"] ?? "collect";
$deliveryTotal = $fulfillmentMethod === "deliver" ? $deliveryFee : 0;
$totalCharged = $itemTotal + $platformFee + $deliveryTotal;

$accessToken = getPayPalAccessToken($apiBase, $clientId, $clientSecret);

if (!$accessToken) {
    echo json_encode([
        "success" => false,
        "message" => "Could not connect to PayPal."
    ]);
    exit();
}

$paypalOrder = createPayPalOrder(
    $apiBase,
    $accessToken,
    $totalCharged,
    "Uni-Shoe Thrifts order"
);

if (!isset($paypalOrder["id"])) {
    echo json_encode([
        "success" => false,
        "message" => "PayPal did not return an order ID."
    ]);
    exit();
}

$_SESSION["paypal_order_id"] = $paypalOrder["id"];
$_SESSION["payment_amount"] = $totalCharged;

echo json_encode([
    "success" => true,
    "order_id" => $paypalOrder["id"]
]);