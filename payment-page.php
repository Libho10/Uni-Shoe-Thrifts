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

$userId = (int) $_SESSION["user_id"];
$cartId = (int) $_SESSION["cart_id"];

// Get buyer details
$getUser = $conn->prepare(
    "SELECT first_name, last_name, email
    FROM users
    WHERE user_id = ?"
);

$getUser->bind_param("i", $userId);
$getUser->execute();
$userResult = $getUser->get_result();

if ($userResult->num_rows === 0) {
    header("Location: login.php");
    exit();
}

$user = $userResult->fetch_assoc();

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

// Get delivery address if delivering
$deliveryAddress = null;

if ($fulfillmentMethod === "deliver") {
    if (empty($_SESSION["checkout_address_id"])) {
        header("Location: review-page.php");
        exit();
    }

    $addressId = (int) $_SESSION["checkout_address_id"];

    $getAddress = $conn->prepare(
        "SELECT address_line_1, address_line_2, suburb, city, province, postal_code
        FROM address
        WHERE address_id = ?
        AND user_id = ?"
    );

    $getAddress->bind_param("ii", $addressId, $userId);
    $getAddress->execute();
    $addressResult = $getAddress->get_result();

    if ($addressResult->num_rows === 0) {
        header("Location: review-page.php");
        exit();
    }

    $deliveryAddress = $addressResult->fetch_assoc();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Payment | Uni-Shoe Thrifts</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.2/font/bootstrap-icons.css">
    <style>

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
            background:  rgba(167, 68, 130, 1);
        }
    </style> 
</head>


<body class="bg-light">

<div class="container pt-4">
    <a href="cart.php" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-2"></i>
        Back to Cart
    </a>
</div>

<div class="container py-5">

     <!-- Stepper -->
    <div class="d-flex align-items-start mb-4">

        <div class="stepper-step stepper-active">
            <div class="stepper-circle">&#10003;</div>
            <small>Review</small>
        </div>

        <div class="stepper-line stepper-line-active"></div>

        <div class="stepper-step stepper-active">
            <div class="stepper-circle">2</div>
            <small class="fw-semibold">Payment</small>
        </div>

        <div class="stepper-line stepper-line-active"></div>

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

        <!-- Left: order and buyer details -->
        <div class="col-lg-7">

            <div class="card mb-4">
                <div class="card-body">

                    <h5 class="mb-3">Your Order</h5>

                    <?php foreach ($cartItems as $item): ?>

                        <div class="row align-items-center border-bottom py-3">

                            <div class="col-3 col-md-2">
                                <img
                                    src="<?= htmlspecialchars($item["main_image"]) ?>"
                                    alt="<?= htmlspecialchars($item["product_name"]) ?>"
                                    class="img-fluid"
                                    style="height:80px; object-fit:cover;"
                                >
                            </div>

                            <div class="col-9 col-md-6">
                                <h6 class="mb-1">
                                    <?= htmlspecialchars($item["product_name"]) ?>
                                </h6>

                                <small class="text-muted d-block">
                                    Size: <?= htmlspecialchars($item["size_value"]) ?>/UK
                                </small>

                                <small class="text-muted d-block">
                                    SKU: <?= htmlspecialchars($item["sku"]) ?>
                                </small>

                                <small class="text-muted d-block">
                                    Quantity: <?= (int) $item["quantity"] ?>
                                </small>
                            </div>

                            <div class="col-6 col-md-2">
                                R<?= number_format($item["price"], 2) ?>
                            </div>

                            <div class="col-6 col-md-2 text-end fw-bold">
                                R<?= number_format($item["line_total"], 2) ?>
                            </div>

                        </div>

                    <?php endforeach; ?>

                </div>
            </div>

            <!-- Fulfilment details -->
            <div class="card mb-4">
                <div class="card-body">

                    <?php if ($fulfillmentMethod === "deliver"): ?>

                        <h5 class="mb-3">Delivering To</h5>

                        <p class="mb-1">
                            <?= htmlspecialchars($user["first_name"] . " " . $user["last_name"]) ?>
                        </p>

                        <p class="mb-0 text-muted">
                            <?= htmlspecialchars($deliveryAddress["address_line_1"]) ?><br>

                            <?php if (!empty($deliveryAddress["address_line_2"])): ?>
                                <?= htmlspecialchars($deliveryAddress["address_line_2"]) ?><br>
                            <?php endif; ?>

                            <?= htmlspecialchars($deliveryAddress["suburb"]) ?><br>
                            <?= htmlspecialchars($deliveryAddress["city"]) ?><br>
                            <?= htmlspecialchars($deliveryAddress["province"]) ?><br>
                            <?= htmlspecialchars($deliveryAddress["postal_code"]) ?>
                        </p>

                    <?php else: ?>

                        <h5 class="mb-3">Collect At</h5>

                        <p class="mb-0 text-muted">
                            <?= htmlspecialchars($collectionAddress) ?>
                        </p>

                    <?php endif; ?>

                </div>
            </div>

            <!-- Buyer details -->
            <div class="card">
                <div class="card-body">

                    <h5 class="mb-3">Buyer</h5>

                    <p class="mb-1 fw-bold">
                        <?= htmlspecialchars($user["first_name"] . " " . $user["last_name"]) ?>
                    </p>

                    <p class="mb-0 text-muted">
                        <?= htmlspecialchars($user["email"]) ?>
                    </p>

                </div>
            </div>

        </div>

        <!-- Right: payment summary -->
        <div class="col-lg-5">

            <div class="card mb-4">
                <div class="card-body">

                    <h5 class="mb-3">Transaction Summary</h5>

                    <div class="d-flex justify-content-between mb-2">
                        <span>Item Price</span>
                        <span>R<?= number_format($itemTotal, 2) ?></span>
                    </div>

                    <div class="d-flex justify-content-between mb-2">
                        <span>Platform Fee</span>
                        <span>R<?= number_format($platformFee, 2) ?></span>
                    </div>

                    <div class="d-flex justify-content-between mb-2">
                        <span>Delivery</span>
                        <span>
                            <?= $fulfillmentMethod === "deliver"
                                ? "R" . number_format($deliveryFee, 2)
                                : "Free" ?>
                        </span>
                    </div>

                    <hr>

                    <div class="d-flex justify-content-between fw-bold">
                        <span>Total Charged</span>
                        <span id="totalChargedText">
                            R<?= number_format($totalCharged, 2) ?>
                        </span>
                    </div>

                </div>
            </div>

            <!-- Payment method -->
            <div class="card">
                <div class="card-body">

                    <h5 class="mb-3">Payment Method</h5>

                    <div class="form-check mb-3">

                        <input
                            class="form-check-input"
                            type="checkbox"
                            id="termsCheckbox"
                        >

                        <label class="form-check-label" for="termsCheckbox">
                            I agree to Uni-Shoe Thrifts Terms of Service and authorise a payment of
                            <strong>R<?= number_format($totalCharged, 2) ?></strong>
                            via PayPal to Uni-Shoe Thrifts Ltd.

                            <?php if ($fulfillmentMethod === "deliver"): ?>
                                Delivery by The Courier Guy
                                (R<?= number_format($deliveryFee, 2) ?>) is included.
                            <?php else: ?>
                                Collection is included at no additional cost.
                            <?php endif; ?>

                            This transaction is protected by PayPal Buyer Protection.
                        </label>

                    </div>

                    <div id="paypal-button-container"></div>

                    <button
                        id="paypalFallbackButton"
                        class="btn btn-primary w-100 mt-3"
                        disabled
                    >
                        Pay R<?= number_format($totalCharged, 2) ?> with PayPal
                    </button>

                </div>
            </div>

        </div>

    </div>

</div>

<script src="https://www.paypal.com/sdk/js?client-id=YOUR_PAYPAL_SANDBOX_CLIENT_ID&currency=ZAR"></script>
<script src="payment-page.js"></script>
</body>
</html>