<?php
session_start();
require_once "connect.php";
$cartItemCount = 0;

if (isset($_SESSION["cart_id"])) {
    $getCartCount = $conn->prepare(
        "SELECT COALESCE(SUM(quantity), 0) AS total_items
        FROM cart_items
        WHERE cart_id = ?
        AND deleted_at IS NULL"
    );

    $getCartCount->bind_param("i", $_SESSION["cart_id"]);
    $getCartCount->execute();
    $cartCountResult = $getCartCount->get_result();
    $cartCountRow = $cartCountResult->fetch_assoc();

    $cartItemCount = (int) $cartCountRow["total_items"];
}

$getProducts = $conn->prepare(
    "SELECT p.product_id, p.product_name, p.summary, p.base_price, p.main_image
    FROM products p
    WHERE p.deleted_at IS NULL
    ORDER BY p.product_id DESC"
);

$getProducts->execute();
$productResult = $getProducts->get_result();
?>

<!DOCTYPE html>
<html lang = en>
    <head>
        <meta charset ="UTF-8">
        <meta name = "viewport" content="width=device-width, iniitial-scale-1.0"> 
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.12.0/font/bootstrap-icons.css">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.2/font/bootstrap-icons.css">
          <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
           <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
     <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Unbounded:wght@200..900&display=swap" rel="stylesheet">

         <link rel="stylesheet" href="index.css">
        <link rel="stylesheet" href="product.css"> 
<!-- Google Identity Services -->
<script src="https://accounts.google.com/gsi/client" async defer></script> <!-- google identity serv-->
<meta name="google-signin-client_id" content="694501172256-5lgva3adqcs6df3ho75907qim3ibi4vu.apps.googleusercontent.com"> <!-- meta tag -->
<!-- -->
        <title> homepage</title>
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
            <section class="category-navigation-sorter-bg py-3 ">
    <div class="container mt-4 mb-2 px-4">
      <div class="row align-items-center">
        <!-- Left: Category navigation with arrows -->
        <div class="col-12 col-md-6 d-flex align-items-center">
          <div class=" pill px-3 py-2 d-flex align-items-center shadow-sm" style="gap:12px;">
            <!-- School shoes(active) -->
              <!-- Arrow to Black -->
            <a href="../Category-Soaps/cat-soaps.html" class="d-flex align-items-center ms-2 text-decoration-none" title="Go to Butters">
            <span style="font-weight:500; color:#68621d">School</span>
            </a>
            <!-- Arrow to Soaps -->
            <a href="../Category-Soaps/cat-soaps.html" class="d-flex align-items-center ms-2 text-decoration-none" title="Go to Soaps">
              <span class="mx-1" style="color:#ccc"><i class="bi bi-chevron-right"></i></span>
              <span style="font-weight:400; color:#666;">Sports</span>
            </a>
            <!-- Arrow to Carrier Oils -->
            <a href="../Category-Carrier-oils/cat-car-oils.html" class="d-flex align-items-center ms-2 text-decoration-none" title="Go to Carrier Oils">
              <span class="mx-1" style="color:#ccc"><i class="bi bi-chevron-right"></i></span>
              <span style="font-weight:400; color:#666;">Boys</span>
            </a>
            <!-- Arrow to Essential Oils -->
            <a href="../Category-Essential-oils/cat-ess-oils.html" class="d-flex align-items-center ms-2 text-decoration-none" title="Go to Essential Oils">
              <span class="mx-1" style="color:#ccc"><i class="bi bi-chevron-right"></i></span>
              <span style="font-weight:400; color:#666;">Girls</span>
            </a>
          </div>
        </div>
         <!-- Right: Sorter -->
        <div class="col-12 col-md-6 d-flex justify-content-md-end align-items-center mt-2 mt-md-0 px-5">
          <span class="me-2" style="font-weight:400;">Sort by</span>
          <select class="form-select w-auto" aria-label="Sort by">
            <option selected>Popularity</option>
            <option value="price_low_high">Price: Low to High</option>
            <option value="price_high_low">Price: High to Low</option>
            <option value="rating">Rating</option>
            <!-- Add more if needed -->
          </select>
        </div>
      </div>
    </div>
  </section>
  <div class="container mt-4 px-4">
  <h1 style="color:#222;  font-family: 'Unbounded', sans-serif; font-weight:300;">All Products</h1>
</div>
  <div class="container py-5">
    <div class="row">


      <!-- Product 1 -->
      <?php while ($product = $productResult->fetch_assoc()): ?>
    <div class="col-12 col-md-6 col-lg-3 text-start mb-4">
        <a
            href="product.php?id=<?= (int) $product["product_id"] ?>"
            style="text-decoration:none; color:inherit;"
        >
            <img
                src="<?= htmlspecialchars($product["main_image"]) ?>"
                alt="<?= htmlspecialchars($product["product_name"]) ?>"
                class="img-fluid mb-3"
                style="width:270px; height:270px; object-fit:contain;"
            >
            <h5 class="mb-2" style="font-family:'Inter',sans-serif; color:#474203; font-weight:300;">
                <?= htmlspecialchars($product["product_name"]) ?>
            </h5>
        </a>
        <div class="mb-1" style="color:#FFD600; font-size:1.1rem;">
            ★★★★★
            <span style="color:#444; font-size:1rem;">(9)</span>
        </div>

        <div class="mb-2 fw-bold" style="color:#222; font-family:'Inter',sans-serif; font-size:1.15rem;">
            R<?= number_format($product["base_price"], 2) ?>
        </div>
        <a
            href="product.php?id=<?= (int) $product["product_id"] ?>"
            class="btn"
            style="color:#474203; font-weight:500; border-radius:10px; border-color:#3A330F;"
        >
            View Product
        </a>
    </div>

<?php endwhile; ?>



    </div>
  </div>
  <footer class="bg-black text-white pt-5 pb-4">
  <div class="container">
    <div class="row mb-4">
      <div class="col-lg-4 mb-4 mb-lg-0">
        <h4 class="mb-3" style="font-weight:700;  font-family: 'Unbounded', sans-serif; ">Uni-Shoe Thrifts</h4>
        <p style="color:#b0b0b0; font-size:1rem;">
          Organic products | Online & Retail | South Africa
        </p>
      </div>
      <div class="col-lg-4 mb-4 mb-lg-0">
        <h5 class="mb-3" style="font-weight:600;">Quick Links</h5>
        <ul class="list-unstyled">
          <li><a href="#" class="text-white-50 text-decoration-none">Home</a></li>
          <li><a href="#" class="text-white-50 text-decoration-none">Shop</a></li>
          <li><a href="#" class="text-white-50 text-decoration-none">About</a></li>
          <li><a href="#" class="text-white-50 text-decoration-none">Contact</a></li>
        </ul>
      </div>
      <div class="col-lg-4 text-lg-end text-center">
        <h5 class="mb-3" style="font-weight:600;">Follow Us</h5>
        <a href="#" class="text-white fs-4 me-3">
          <i class="bi bi-twitter"></i>
        </a>
        <a href="#" class="text-white fs-4 me-3">
          <i class="bi bi-facebook"></i>
        </a>
        <a href="#" class="text-white fs-4 me-3">
          <i class="bi bi-instagram"></i>
        </a>
        <a href="#" class="text-white fs-4 me-3">
          <i class="bi bi-tiktok"></i>
        </a>
      </div>
    </div>
    <hr style="border-color:rgba(255,255,255,0.15);" />
    <div class="row">
      <div class="col text-center" style="font-size:0.95rem;color:#888;">
        &copy; 2026 Uni-Shoe Thrifts. All rights reserved.
      </div>
    </div>
  </div>
</footer>
        </body>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
      
        <script src="index.js"></script>
</html>
