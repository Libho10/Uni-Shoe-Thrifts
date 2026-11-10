<?php
session_start();
require_once "connect.php";

$cartItemCount = 0;
$cartItems = [];
$cartTotal = 0;

if (isset($_SESSION["cart_id"])) {
    $cartId = $_SESSION["cart_id"];

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

    while ($item = $cartItemsResult->fetch_assoc()) {
        $item["line_total"] = $item["price"] * $item["quantity"];
        $cartTotal += $item["line_total"];
        $cartItems[] = $item;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Your Cart | Uni-Shoe Thrifts</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.2/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
     <link rel="stylesheet" href="cart.css"> 

    <style>
        body {
           font-family: -apple-system, BlinkMacSystemFont, sans-serif;
            background-color: #f6f7f4;
        }

        .cart-card {
            background: #fff;
            border: none;
            border-radius: 16px;
            box-shadow: 0 6px 24px rgba(0, 0, 0, 0.06);
        }

        .cart-product-image {
            width: 110px;
            height: 110px;
            object-fit: cover;
            border-radius: 12px;
        }

        .quantity-control {
            max-width: 130px;
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

        .summary-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 12px;
        }

        .remove-btn {
            color: #dc3545;
            font-size: 1.2rem;
        }

        .remove-btn:hover {
            color: #a71d2a;
        }
    </style>
</head>

<body>

  <nav class="navbar navbar-expand-lg bg-body-tertiary">
                <div class="container-fluid mx-5">
                        <a class="navbar-brand" href="index.php">
                                    <img src="UTH.png" alt="Logo" width="55" height="55">
                        </a>
                        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                            <span class="navbar-toggler-icon"></span>
                        </button>
                    <div class="collapse navbar-collapse" id="navbarSupportedContent">
                            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                                <li class="nav-item"> <a class="nav-link active" aria-current="page" href="#"> New and Featured</a></li>
                                <li class="nav-item"> <a class="nav-link active" href="#">Boys</a></li>
                                <li class="nav-item"> <a class="nav-link active" href="#">Girls</a></li>
                                <li class="nav-item"><a class="nav-link active" href="#">Sale</a></li>                              
                                <li class="nav-item dropdown">
                                    <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">Sports</a>
                                    <ul class="dropdown-menu">
                                        <li><a class="dropdown-item" href="#">Tennis</a></li>
                                        <li><a class="dropdown-item" href="#">Soccer</a></li>
                                        <li><a class="dropdown-item" href="#">Netball</a></li>
                                        <li><a class="dropdown-item" href="#">Rugby</a></li>
                                        <li><hr class="dropdown-divider"></li>
                                        <li><a class="dropdown-item" href="#">See All</a></li>
                                    </ul>
                                </li>
                                <li class="nav-item dropdown">
                                 <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false"> School</a>
                                    <ul class="dropdown-menu">
                                        <li><a class="dropdown-item" href="#">Closed</a></li>
                                        <li><a class="dropdown-item" href="#"> Open shoe</a></li>
                                        <li><a class="dropdown-item" href="product.php?id=3">Black</a></li>
                                        <li><a class="dropdown-item" href="#">Brown</a></li>
                                        <li><hr class="dropdown-divider"></li>
                                        <li><a class="dropdown-item" href="products.php">See All...</a></li>
                                    </ul>
                                </li>                           
                            </ul>
                            <ul class="nav justify-content-end">
                                <li class="nav-item position-relative">
                                    <a class="navbar-brand" href="cart.php">
                                    <img src="cart.svg" alt="Cart" width="20" height="20">
                                     <span
                                    id="cartBadge"
                                    class="position-absolute translate-middle badge rounded-pill bg-danger"
                                    style="top:10px; left:100%; <?= $cartItemCount > 0 ? "" : "display:none;" ?>"
                                >
                                    <?= $cartItemCount ?>
                                </span>
                                    </a>
                                </li>
                                <li class="nav-item">
                                   <a class="navbar-brand" href="#">
                                    <img src="heart.svg" alt="Logo" width="20" height="20">
                                    </a>
                                </li>
                                <li class="nav-item">
                      <a href="../account-logo.svg" id="openAccountLink" data-bs-toggle="modal" data-bs-target="#accountModal">
                                <i class="bi bi-person" style="font-size:1.6rem;"></i>            
                                </a>                      
                                </li>
                                <li class="nav-item">
                                   <button class="btn btn-outline-success btn-contact-us" type="submit">Contact Us</button>
                                </li>
                            </ul>
                    </div>
                </div>
            </nav>

<div class="container py-5">

    <div class="d-flex align-items-center justify-content-between mb-4">

        <div>
            <h2 class="mb-1">Your Cart</h2>
            <p class="text-muted mb-0">
                Review your selected items before checkout.
            </p>
        </div>

        <a href="products.php" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-2"></i>
            Continue Shopping
        </a>

    </div>

    <?php if (empty($cartItems)): ?>

        <div class="cart-card p-5 text-center">

            <i class="bi bi-cart-x" style="font-size:4rem; color:#adb5bd;"></i>

            <h3 class="mt-3 mb-2">Your cart is empty</h3>

            <p class="text-muted mb-4">
                Browse our available shoes and add something you love.
            </p>

            <a href="products.php" class="btn btn-brand px-4">
                Start Shopping
            </a>

        </div>

    <?php else: ?>

        <div class="row g-4">

            <!-- Cart items -->
            <div class="col-lg-8">

                <div class="cart-card p-4">

                    <div class="d-flex justify-content-between align-items-center mb-3">

                        <h5 class="mb-0">
                            Cart Items
                        </h5>

                        <span class="text-muted">
                            <?= count($cartItems) ?>
                            <?= count($cartItems) === 1 ? "item" : "items" ?>
                        </span>

                    </div>

                    <?php foreach ($cartItems as $item): ?>

                        <div class="cart-item-row d-flex flex-column flex-md-row gap-3 align-items-md-center border-bottom py-3">

                            <a href="product.php?id=<?= (int) $item["product_id"] ?>">
                                <img
                                    src="<?= htmlspecialchars($item["main_image"]) ?>"
                                    alt="<?= htmlspecialchars($item["product_name"]) ?>"
                                    class="cart-product-image"
                                >
                            </a>

                            <div class="flex-grow-1">

                                <a
                                    href="product.php?id=<?= (int) $item["product_id"] ?>"
                                    class="text-decoration-none text-dark"
                                >
                                    <h6 class="mb-1">
                                        <?= htmlspecialchars($item["product_name"]) ?>
                                    </h6>
                                </a>

                                <div class="text-muted small">
                                    Size: <?= htmlspecialchars($item["size_value"]) ?>/UK<br>
                                    SKU: <?= htmlspecialchars($item["sku"]) ?>
                                </div>

                            </div>

                            <div class="text-md-center">

                                <div class="text-muted small mb-1">
                                    R<?= number_format($item["price"], 2) ?> each
                                </div>

                                <input
                                    type="number"
                                    class="form-control form-control-sm text-center cart-quantity quantity-control"
                                    min="1"
                                    value="<?= (int) $item["quantity"] ?>"
                                    data-cart-item-id="<?= (int) $item["cart_item_id"] ?>"
                                    data-unit-price="<?= htmlspecialchars($item["price"]) ?>"
                                >

                            </div>

                            <div class="text-md-end" style="min-width:110px;">

                                <div class="text-muted small mb-1">
                                    Line Total
                                </div>

                                <div class="fw-bold">
                                    R<span class="item-line-total" data-cart-item-id="<?= (int) $item["cart_item_id"] ?>">
                                        <?= number_format($item["line_total"], 2) ?>
                                    </span>
                                </div>

                            </div>

                            <div class="text-md-end">

                                <button
                                    type="button"
                                    class="btn btn-link remove-btn remove-cart-item p-0"
                                    data-cart-item-id="<?= (int) $item["cart_item_id"] ?>"
                                    aria-label="Remove item"
                                >
                                    <i class="bi bi-trash"></i>
                                </button>

                            </div>

                        </div>

                    <?php endforeach; ?>

                </div>

            </div>

            <!-- Order summary -->
            <div class="col-lg-4">

                <div class="cart-card p-4 sticky-top" style="top:20px;">

                    <h5 class="mb-4">Order Summary</h5>

                    <div class="summary-row">
                        <span class="text-muted">Items</span>
                        <span><?= count($cartItems) ?></span>
                    </div>

                    <div class="summary-row">
                        <span class="text-muted">Subtotal</span>
                        <span id="cartSubtotal">
                            R<?= number_format($cartTotal, 2) ?>
                        </span>
                    </div>

                    <div class="summary-row">
                        <span class="text-muted">Delivery</span>
                        <span>Calculated at checkout</span>
                    </div>

                    <hr>

                    <div class="summary-row fw-bold">
                        <span>Total</span>
                        <span id="cartTotal">
                            R<?= number_format($cartTotal, 2) ?>
                        </span>
                    </div>

                    <a href="review-page.php" class="btn btn-brand w-100 mt-3">
                        Proceed to Checkout
                        <i class="bi bi-arrow-right ms-2"></i>
                    </a>

                    <a href="products.php" class="btn btn-outline-secondary w-100 mt-2">
                        Continue Shopping
                    </a>

                    <div class="text-center text-muted small mt-3">
                        <i class="bi bi-shield-check me-1"></i>
                        Secure checkout powered by PayPal
                    </div>

                </div>

            </div>

        </div>

    <?php endif; ?>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="cart.js"></script>
</body>
</html>