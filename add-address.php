<?php
session_start();

header("Content-Type: application/json; charset=UTF-8");

require_once "connect.php";

function sendJson($success, $message) {
    echo json_encode([
        "success" => $success,
        "message" => $message
    ]);
    exit();
}

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    sendJson(false, "Only POST requests are allowed.");
}

if (empty($_SESSION["logged_in"]) || empty($_SESSION["user_id"])) {
    sendJson(false, "Please log in before saving an address.");
}

$userId = (int) $_SESSION["user_id"];

$title = trim($_POST["title"] ?? "");
$addressLine1 = trim($_POST["address_line_1"] ?? "");
$addressLine2 = trim($_POST["address_line_2"] ?? "");
$suburb = trim($_POST["suburb"] ?? "");
$city = trim($_POST["city"] ?? "");
$province = trim($_POST["province"] ?? "");
$postalCode = trim($_POST["postal_code"] ?? "");
$phoneNumber = trim($_POST["phone_number"] ?? "");

if ($addressLine1 === "" || $suburb === "" || $city === "" || $province === "" || $postalCode === "" || $phoneNumber === "") {
    sendJson(false, "Please complete all required address fields.");
}

if (!preg_match("/^[0-9]{4}$/", $postalCode)) {
    sendJson(false, "Please enter a valid 4-digit postal code.");
}

$insertAddress = $conn->prepare(
    "INSERT INTO address
    (user_id, title, address_line_1, address_line_2, suburb, city, province, postal_code, phone_number, created_at)
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())"
);

if (!$insertAddress) {
    sendJson(false, "Could not prepare the address query.");
}

$insertAddress->bind_param(
    "issssssss",
    $userId,
    $title,
    $addressLine1,
    $addressLine2,
    $suburb,
    $city,
    $province,
    $postalCode,
    $phoneNumber
);

if (!$insertAddress->execute()) {
    $error = $insertAddress->error;

    $insertAddress->close();
    $conn->close();

    sendJson(false, "Could not save your address: " . $error);
}
$addressId = $insertAddress->insert_id;
$insertAddress->close();
$conn->close();

$_SESSION["checkout_address_id"] = $addressId;
$_SESSION["fulfillment_method"] = "deliver";

sendJson(true, "Your address was saved successfully.");