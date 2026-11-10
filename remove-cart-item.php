<?php
session_start();
require_once "connect.php";

header("Content-Type: application/json");

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    echo json_encode(["success" => false]);
    exit();
}

$cartItemId = (int) ($_POST["cart_item_id"] ?? 0);

if ($cartItemId <= 0 || !isset($_SESSION["cart_id"])) {
    echo json_encode(["success" => false]);
    exit();
}

$remove = $conn->prepare(
    "UPDATE cart_items
    SET deleted_at = NOW()
    WHERE cart_item_id = ?
    AND cart_id = ?
    AND deleted_at IS NULL"
);

$remove->bind_param("ii", $cartItemId, $_SESSION["cart_id"]);
$remove->execute();

echo json_encode(["success" => true]);