<?php
session_start();
require_once "connect.php";

header("Content-Type: application/json");

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    echo json_encode(["success" => false, "message" => "Invalid request."]);
    exit();
}
if (!isset($_SESSION["user_id"]) || (int) $_SESSION["user_id"] <= 0) {
    echo json_encode([
        "success" => false,
        "login_required" => true,
        "message" => "Please sign up or log in to add items to your cart."
    ]);
    exit();
}

$userId = (int) $_SESSION["user_id"];

$variantId = (int) ($_POST["product_variant_id"] ?? 0);
$quantity = (int) ($_POST["quantity"] ?? 1);

if ($variantId <= 0 || $quantity <= 0) {
    echo json_encode(["success" => false, "message" => "Please select a valid size and quantity."]);
    exit();
}

// Check the variant exists and has stock
$getVariant = $conn->prepare(
    "SELECT product_variant_id, price, quantity
    FROM product_variants
    WHERE product_variant_id = ?
    AND deleted_at IS NULL"
);

$getVariant->bind_param("i", $variantId);
$getVariant->execute();
$variantResult = $getVariant->get_result();

if ($variantResult->num_rows === 0) {
    echo json_encode(["success" => false, "message" => "This product size is unavailable."]);
    exit();
}

$variant = $variantResult->fetch_assoc();

if ($quantity > $variant["quantity"]) {
    echo json_encode([
        "success" => false,
        "message" => "Only " . $variant["quantity"] . " item(s) available."
    ]);
    exit();
}

// Find the logged-in user's active cart
$getCart = $conn->prepare(
    "SELECT cart_id
    FROM carts
    WHERE user_id = ?
    AND deleted_at IS NULL
    ORDER BY cart_id DESC
    LIMIT 1"
);

$getCart->bind_param("i", $userId);
$getCart->execute();
$cartResult = $getCart->get_result();

if ($cartResult->num_rows > 0) {
    $cart = $cartResult->fetch_assoc();
    $_SESSION["cart_id"] = (int) $cart["cart_id"];
} else {
    $createCart = $conn->prepare(
        "INSERT INTO carts (user_id, created_at)
        VALUES (?, NOW())"
    );

    $createCart->bind_param("i", $userId);
    $createCart->execute();

    $_SESSION["cart_id"] = $createCart->insert_id;
}

$cartId = $_SESSION["cart_id"];

// If the same variant is already in the cart, increase quantity
$checkItem = $conn->prepare(
    "SELECT cart_id, quantity
    FROM cart_items
    WHERE cart_id = ?
    AND product_variant_id = ?
    AND deleted_at IS NULL"
);

$checkItem->bind_param("ii", $cartId, $variantId);
$checkItem->execute();
$existingItemResult = $checkItem->get_result();

if ($existingItemResult->num_rows > 0) {
    $existingItem = $existingItemResult->fetch_assoc();

    $newQuantity = $existingItem["quantity"] + $quantity;

    if ($newQuantity > $variant["quantity"]) {
        echo json_encode([
            "success" => false,
            "message" => "Only " . $variant["quantity"] . " item(s) available."
        ]);
        exit();
    }

    $updateItem = $conn->prepare(
        "UPDATE cart_items
        SET quantity = ?, updated_at = NOW()
        WHERE cart_id = ?"
    );

    $updateItem->bind_param("ii", $newQuantity, $existingItem["cart_id"]);
    $updateItem->execute();
} else {
    $insertItem = $conn->prepare(
        "INSERT INTO cart_items (
            cart_id,
            product_variant_id,
            quantity,
            created_at
        )
        VALUES (?, ?, ?, NOW())"
    );

    $insertItem->bind_param("iii", $cartId, $variantId, $quantity);
    $insertItem->execute();
}

// Return the updated cart count
$getCount = $conn->prepare(
    "SELECT COALESCE(SUM(quantity), 0) AS total_items
    FROM cart_items
    WHERE cart_id = ?
    AND deleted_at IS NULL"
);

$getCount->bind_param("i", $cartId);
$getCount->execute();
$countResult = $getCount->get_result();
$countRow = $countResult->fetch_assoc();

echo json_encode([
    "success" => true,
    "message" => "Added to cart.",
    "cart_count" => (int) $countRow["total_items"]
]);