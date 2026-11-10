<?php
session_start();
require_once "connect.php";

if (empty($_SESSION["logged_in"]) || empty($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
} 

if (empty($_SESSION["cart_id"])) {
    header("Location: cart.php");
    exit();
}

$cartId = (int) $_SESSION["cart_id"];

$getCartItems = $conn->prepare(
    "SELECT 
        ci.cart_item_id,
        ci.quantity,
        pv.product_variant_id,
        pv.sku,
        pv.price,
        p.product_id,
        p.product_name,
        p.main_image,
        MAX(av.value) AS size_value
    FROM cart_items ci
    INNER JOIN product_variants pv
        ON pv.product_variant_id = ci.product_variant_id
    INNER JOIN products p
        ON p.product_id = pv.product_id
    INNER JOIN product_variant_attributes pva
        ON pva.product_variant_id = pv.product_variant_id
    INNER JOIN attributes a
        ON a.attribute_id = pva.attribute_id
    INNER JOIN attribute_values av
        ON av.value_id = pva.value_id
    WHERE ci.cart_id = ?
    AND ci.deleted_at IS NULL
    AND a.name = 'Size'
    GROUP BY 
        ci.cart_item_id,
        ci.quantity,
        pv.product_variant_id,
        pv.sku,
        pv.price,
        p.product_id,
        p.product_name,
        p.main_image
    ORDER BY ci.cart_item_id DESC"
);

$getCartItems->bind_param("i", $cartId);
$getCartItems->execute();
$cartItemsResult = $getCartItems->get_result();

$cartItems = [];
$itemTotal = 0;

while ($item = $cartItemsResult->fetch_assoc()) {
    $item["line_total"] = $item["price"] * $item["quantity"];
    $itemTotal += $item["line_total"];
    $cartItems[] = $item;
}

if (empty($cartItems)) {
    header("Location: cart.php");
    exit();
}

$platformFee = 1.50;
$deliveryFee = 23.50;
$collectionAddress = "14 Bree Street, Samrand, Centurion, Gauteng 1345";

$fulfillmentMethod = $_SESSION["fulfillment_method"] ?? "collect";
$deliveryTotal = $fulfillmentMethod === "deliver" ? $deliveryFee : 0;
$totalCharged = $itemTotal + $platformFee + $deliveryTotal;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Review Order | Uni-Shoe Thrifts</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.2/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, sans-serif;
            background-color: #f6f7f4;
        }

        .checkout-card {
            background: #fff;
            border: none;
            border-radius: 16px;
            box-shadow: 0 6px 24px rgba(0, 0, 0, 0.06);
        }

        .stepper-step {
            text-align: center;
            flex: 1;
        }

        .stepper-circle {
            width: 42px;
            height: 42px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 8px;
            font-weight: 600;
            background: #e9ecef;
            color: #6c757d;
        }

        .stepper-active .stepper-circle {
            background:  rgba(167, 68, 130, 1);
            color: #fff;
        }

        .stepper-line {
            flex: 1;
            height: 3px;
            background: #dee2e6;
            margin-top: 20px;
        }

        .stepper-line-active {
            background: rgba(167, 68, 130, 1);
        }

        .product-image {
            width: 100%;
            height: 110px;
            object-fit: cover;
            border-radius: 10px;
        }

        .fulfillment-btn {
            border-radius: 12px;
            padding: 12px 18px;
            font-weight: 500;
        }

        .summary-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
        }

        .btn-brand {
            background: #54B709;
            color: #fff;
            border-radius: 12px;
            font-weight: 600;
        }

        .btn-brand:hover {
            background: #47a007;
            color: #fff;
        }
    </style>
</head>

<body>

<div class="container py-4">

    <!-- Back button -->
    <a href="cart.php" class="btn btn-outline-secondary mb-3">
        <i class="bi bi-arrow-left me-2"></i>
        Back to Cart
    </a>

    <!-- Stepper -->
    <div class="d-flex align-items-start mb-4">

        <div class="stepper-step stepper-active">
            <div class="stepper-circle">1</div>
            <small class="fw-semibold">Review</small>
        </div>

        <div class="stepper-line stepper-line-active"></div>

        <div class="stepper-step">
            <div class="stepper-circle">2</div>
            <small>Payment</small>
        </div>

        <div class="stepper-line"></div>

        <div class="stepper-step">
            <div class="stepper-circle">3</div>
            <small>Confirmation</small>
        </div>

        <div class="stepper-line"></div>

        <div class="stepper-step">
            <div class="stepper-circle">4</div>
            <small>Complete</small>
        </div>

    </div>

    <div class="row g-4">

        <!-- Left column -->
        <div class="col-lg-7">

            <!-- Order review -->
            <div class="checkout-card p-4 mb-4">

                <h4 class="mb-4">Review Your Order</h4>

                <?php foreach ($cartItems as $item): ?>

                    <div class="d-flex gap-3 align-items-center border-bottom pb-3 mb-3">

                        <img
                            src="<?= htmlspecialchars($item["main_image"]) ?>"
                            alt="<?= htmlspecialchars($item["product_name"]) ?>"
                            class="product-image"
                            style="width:110px;"
                        >

                        <div class="flex-grow-1">

                            <h6 class="mb-1">
                                <?= htmlspecialchars($item["product_name"]) ?>
                            </h6>

                            <div class="text-muted small">
                                Size: <?= htmlspecialchars($item["size_value"]) ?>/UK<br>
                                SKU: <?= htmlspecialchars($item["sku"]) ?><br>
                                Quantity: <?= (int) $item["quantity"] ?>
                            </div>

                        </div>

                        <div class="text-end">

                            <div class="text-muted small mb-1">
                                R<?= number_format($item["price"], 2) ?> each
                            </div>

                            <div class="fw-bold">
                                R<?= number_format($item["line_total"], 2) ?>
                            </div>

                        </div>

                    </div>

                <?php endforeach; ?>

            </div>

            <!-- Fulfilment -->
            <div class="checkout-card p-4 mb-4">

                <h5 class="mb-3">Fulfilment Method</h5>

                <div class="d-flex flex-wrap gap-3 mb-4">

                    <button
                        type="button"
                        id="collectButton"
                        class="btn fulfillment-btn btn-success"
                    >
                        <i class="bi bi-shop me-2"></i>
                        Collect — Free
                    </button>

                    <button
                        type="button"
                        id="deliverButton"
                        class="btn fulfillment-btn btn-outline-secondary"
                    >
                        <i class="bi bi-truck me-2"></i>
                        Deliver — The Courier Guy, R23.50
                    </button>

                </div>

                <!-- Collection -->
                <div id="collectionBlock">

                    <h6 class="text-muted mb-2">Collect At</h6>

                    <div class="border rounded p-3 bg-light">
                        <i class="bi bi-geo-alt me-2"></i>
                        14 Bree Street, Samrand, Centurion, Gauteng 1345
                    </div>

                </div>

                <!-- Delivery -->
                <div id="deliveryBlock" class="d-none">

                    <h6 class="text-muted mb-2">Delivery Address</h6>

                    <form action="add-address.php" method="POST" id="addressForm">

                        <div class="row g-2">

                            <div class="col-12">
                                <input type="text" class="form-control" name="address_line_1" placeholder="Street Address" required>
                            </div>

                            <div class="col-12">
                                <input type="text" class="form-control" name="address_line_2" placeholder="Apartment, unit, complex (optional)">
                            </div>

                            <div class="col-md-6">
                                <input type="text" class="form-control" name="suburb" placeholder="Suburb" required>
                            </div>

                            <div class="col-md-6">
                                <input type="text" class="form-control" name="city" placeholder="City" required>
                            </div>

                            <div class="col-md-6">
                                <select class="form-select" name="province" required>
                                    <option value="">Select province</option>
                                    <option value="Gauteng">Gauteng</option>
                                    <option value="Western Cape">Western Cape</option>
                                    <option value="KwaZulu-Natal">KwaZulu-Natal</option>
                                    <option value="Eastern Cape">Eastern Cape</option>
                                    <option value="Free State">Free State</option>
                                    <option value="Limpopo">Limpopo</option>
                                    <option value="Mpumalanga">Mpumalanga</option>
                                    <option value="North West">North West</option>
                                    <option value="Northern Cape">Northern Cape</option>
                                </select>
                            </div>

                            <div class="col-md-6">
                                <input type="text" class="form-control" name="postal_code" placeholder="Postal code" maxlength="4" pattern="[0-9]{4}" required>
                            </div>

                            <div class="col-12">
                                <input type="tel" class="form-control" name="phone_number" placeholder="Phone number" required>
                            </div>

                            <div class="col-12">
                                <button class="btn btn-brand w-100" type="submit">
                                    Save Address
                                </button>
                            </div>

                        </div>

                    </form>

                </div>

            </div>

        </div>

        <!-- Right column -->
        <div class="col-lg-5">

            <div class="checkout-card p-4">

                <h5 class="mb-4">Transaction Summary</h5>

                <div class="summary-row">
                    <span class="text-muted">Item Price</span>
                    <span id="itemTotalText">
                        R<?= number_format($itemTotal, 2) ?>
                    </span>
                </div>

                <div class="summary-row">
                    <span class="text-muted">Platform Fee</span>
                    <span>R1.50</span>
                </div>

                <div class="summary-row">
                    <span class="text-muted">Delivery</span>
                    <span id="deliveryFeeText">Free</span>
                </div>

                <hr>

                <div class="summary-row fw-bold">
                    <span>Total Charged</span>
                    <span id="totalChargedText">
                        R<?= number_format($itemTotal + 1.50, 2) ?>
                    </span>
                </div>

                <input type="hidden" id="itemTotalHidden" value="<?= $itemTotal ?>">

                <a
                    href="payment-page.php"
                    id="continueToPayment"
                    class="btn btn-brand w-100 mt-4"
                >
                    Continue to Payment
                    <i class="bi bi-arrow-right ms-2"></i>
                </a>

                <a href="cart.php" class="btn btn-outline-secondary w-100 mt-2">
                    <i class="bi bi-arrow-left me-2"></i>
                    Back to Cart
                </a>

            </div>

        </div>

    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="review-page.js"></script>
</body>
</html>



