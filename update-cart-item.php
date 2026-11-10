<?php
session_start();
require_once "connect.php";

header("Content-Type: application/json");

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    echo json_encode(["success" => false]);
    exit();
}

$cartItemId = (int) ($_POST["cart_item_id"] ?? 0);
$quantity = (int) ($_POST["quantity"] ?? 0);

if ($cartItemId <= 0 || $quantity <= 0 || !isset($_SESSION["cart_id"])) {
    echo json_encode(["success" => false]);
    exit();
}

$update = $conn->prepare(
    "UPDATE cart_items
    SET quantity = ?, updated_at = NOW()
    WHERE cart_item_id = ?
    AND cart_id = ?
    AND deleted_at IS NULL"
);

$update->bind_param("iii", $quantity, $cartItemId, $_SESSION["cart_id"]);
$update->execute();

echo json_encode(["success" => true]);