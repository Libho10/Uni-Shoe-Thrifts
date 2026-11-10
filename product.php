<?php
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

$productId = (int) ($_GET["id"] ?? 0);

if ($productId <= 0) {
    header("Location: products.php");
    exit();
}
$getProduct = $conn->prepare(
    "SELECT product_id, product_name, description, summary, base_price, main_image
    FROM products
    WHERE product_id = ?
    AND deleted_at IS NULL"
);

$getProduct->bind_param("i", $productId);
$getProduct->execute();
$productResult = $getProduct->get_result();

if ($productResult->num_rows === 0) {
    header("Location: products.php");
    exit();
}

$product = $productResult->fetch_assoc();

$getImages = $conn->prepare(
    "SELECT image_id, image_path
    FROM product_images
    WHERE product_id = ?
    AND deleted_at IS NULL
    ORDER BY image_id ASC"
);

$getImages->bind_param("i", $productId);
$getImages->execute();
$imageResult = $getImages->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <!-- Google Identity Services -->
<script src="https://accounts.google.com/gsi/client" async defer></script> <!-- google identity serv-->
<meta name="google-signin-client_id" content="694501172256-5lgva3adqcs6df3ho75907qim3ibi4vu.apps.googleusercontent.com"> <!-- meta tag -->
<!-- -->
  
  <title><?= htmlspecialchars($product["product_name"]) ?> | Uni-Shoe Thrifts</title>

  <!-- Bootstrap CSS CDN -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"/>
  <!-- Bootstrap Icons CDN -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.12.0/font/bootstrap-icons.css">

  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.2/font/bootstrap-icons.css">
  <!-- Your custom CSS -->
  <link rel="stylesheet" href="product.css" />
   <link rel="stylesheet" href="index.css" />

  <link href="https://fonts.googleapis.com/css2?family=Inter:ital,wght@1,700&display=swap" rel="stylesheet">

  <link href="https://fonts.googleapis.com/css?family=Inter:100,200,300,400,500,600,700,800,900" rel="stylesheet">
</head>
 

<body class="d-flex flex-column min-vh-100">

   
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
                                        <li><a class="dropdown-item" href="./CocoaButter-FoodGrade/CocoaButter-FoodGrade.html">Black</a></li>
                                        <li><a class="dropdown-item" href="#">Brown</a></li>
                                        <li><hr class="dropdown-divider"></li>
                                        <li><a class="dropdown-item" href="#">See All...</a></li>
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
                      <a href="#" id="openAccountLink" data-bs-toggle="modal" data-bs-target="#accountModal">
                                <i class="bi bi-person" style="font-size:1.6rem; color:#000000"></i>
                
                                </a>

                
                                
                                </li>
                                <li class="nav-item">
                                   <button class="btn btn-outline-success btn-contact-us" type="submit">Contact Us</button>
                                </li>
                            </ul>
                    </div>
                </div>
            </nav>

 <!-- Category navigation with arrows and sorter -->

<section class="category-navigation-sorter-bg py-3 ">
    <div class="container mt-4 mb-2 px-4">
      <div class="row align-items-center">
        <!-- Left: Category navigation with arrows -->
        <div class="col-12 col-md-6 d-flex align-items-center">
          <div class=" pill px-3 py-2 d-flex align-items-center shadow-sm" style="gap:12px;">
            <!-- Butters (active) -->
             <a href="../School-section/school-section.html" class="d-flex align-items-center ms-2 text-decoration-none" title="Go to Soaps">
            <span style="font-weight:400; color:#666">School</span>
             </a>
            <!-- Arrow to Soaps -->
            <a href="cat-soaps.html" class="d-flex align-items-center ms-2 text-decoration-none" title="Go to Soaps">
              <span class="mx-1" style="color:#ccc"><i class="bi bi-chevron-right"></i></span>
              <span style="font-weight:500; color:#68621d;">Black</span>
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

<section class="flex-grow-1">

<div class="container px-4 py-5">
  <div class="row g-4 align-items-start">
    <!-- LEFT COLUMN: Product Image -->

    <!-- LEFT SIDE: Thumbnails + Main Image -->
<div class="col-md-6">
  <div class="row g-3">

    <!-- Thumbnail column -->
    <div class="col-2 d-flex flex-column gap-3">

      
    <?php while ($image = $imageResult->fetch_assoc()): ?>

        <img
            src="<?= htmlspecialchars($image["image_path"]) ?>"
            alt="Product image"
            class="img-thumbnail product-thumbnail <?= $image["image_id"] == 1 ? "active" : "" ?>"
            data-full-image="<?= htmlspecialchars($image["image_path"]) ?>"
        >

    <?php endwhile; ?>

    </div>

    <!-- Main image -->
    <div class="col-10 text-center">
      <img
    id="mainProductImage"
    src="<?= htmlspecialchars($product["main_image"]) ?>"
    alt="<?= htmlspecialchars($product["product_name"]) ?>"
    class="img-fluid"
    style="max-width:100%; width:650px; height:auto;"
>
    </div>

  </div>
</div>



    <!-- RIGHT COLUMN: Product Details -->
    <div class="col-md-6 px-4">
       <span class="badge mb-2" style="font-size:1rem; font-weight:400; background:#33d90025; color:#54B709; border:1.6px solid #54B709; border-radius:5px;">
        In Stock
      </span>
      <h1 class="fw-light mb-2" style="color:#48401a; font-family:'Inter',sans-serif; font-size:1.9rem; line-height:1.15;">
         <?= htmlspecialchars($product["product_name"]) ?>
      </h1>
     
      <div class="mt-2 mb-2" style="font-size:1.2rem;">
        <span style="color:#FFD600;">★★★★★</span>
        <span class="ms-2" style="color:#444; font-size:1rem;">4.8 (9 Reviews)</span>
      </div>
      <div class="mb-3">
        <span id="product-price" style="font-weight:700; font-size:1.5rem; color:#333; transition:color 0.4s,transform 0.4s;">
          R<?= number_format($product["base_price"], 2) ?>
         </span>
       </div>

      <p style="color:#888; font-size:1rem; max-width:6q00px;">
       <?= htmlspecialchars($product["description"]) ?>
      </p>
      <p class="mb-2" style="color:#616c31;">Bulk Orders: <span style="color:#232323;">For bulk quantities, we offer custom prices.</span></p>
      <p class="mb-2" style="color:#616c31; font-size:1rem;">
        <i class="bi bi-envelope me-1"></i> admin@unishoethrift.com
      </p>

      <?php
    $getVariants = $conn->prepare(
    "SELECT pv.product_variant_id, pv.sku, pv.price, pv.quantity, av.value AS size_value
    FROM product_variants pv
    INNER JOIN product_variant_attributes pva
        ON pva.product_variant_id = pv.product_variant_id
    INNER JOIN attributes a
        ON a.attribute_id = pva.attribute_id
    INNER JOIN attribute_values av
        ON av.value_id = pva.value_id
    WHERE pv.product_id = ?
    AND a.name = 'Size'
    AND pv.deleted_at IS NULL
    ORDER BY CAST(av.value AS DECIMAL(10,1)) ASC"
);

    $getVariants->bind_param("i", $productId);
    $getVariants->execute();
    $variantResult = $getVariants->get_result();
    ?>
      

      <div class="mb-3 " style="font-weight:500;">

      
        Available Sizes
        <div class="d-flex flex-wrap gap-3 mt-2">

    <?php $sizeCount = 0; ?>

    <?php while ($variant = $variantResult->fetch_assoc()): $sizeCount++; ?>

        <button
            class="btn size-btn px-4 py-2 <?= $sizeCount === 1 ? "active" : "" ?>"
            type="button"
            data-variant-id="<?= (int) $variant["product_variant_id"] ?>"
            data-price="<?= htmlspecialchars($variant["price"]) ?>"
            data-quantity="<?= (int) $variant["quantity"] ?>"
            data-sku="<?= htmlspecialchars($variant["sku"]) ?>"
            style="background:#fff; border:1px solid #d4d4d2; border-radius:10px; min-width:85px; height:60px;"
        >
            <?= htmlspecialchars($variant["size_value"]) ?>/UK
        </button>

    <?php endwhile; ?>

</div>

      </div>

     <div class="d-flex flex-wrap align-items-center gap-2 mb-3">
      <div class="d-flex border" style="max-width:116px;">
        <button class="btn px-2" id="qty-minus" style="border:none; background:transparent; color:#44401a; font-size:1.3rem;">–</button>

        <input type="number" id="qty-input" value="1" min="1" step="1"
        class="form-control border-0 text-center"style="width:40px;padding:3px 0; font-size:1.1rem; background:transparent;">

        <button class="btn px-2" id="qty-plus" style="border:none; background:transparent; color:#44401a; font-size:1.3rem;">+</button>
     </div>

        <button id="addToCartButton" class="btn px-4 py-2" type="button" style="background:rgba(182, 251, 177); color:#44401a; border-radius:10px; font-weight:600;" > Add to Cart
        </button>
        <button class="btn px-4 py-2" style="background: rgba(167, 68, 130, 1); color:#fff; border-radius:10px; font-weight:600;">Buy Now</button>
        <button class="btn px-4 py-2" style="background:transparent; color:#000000; font-size:1.4rem; border:none;"><i class="bi bi-heart"></i></button>
      </div>

      <div class="mt-3">
        <span id="product-sku">SKU: N/A</span>
        <span style="color:#888;display:block;">Tags: <span style="color:#444;">School</span></span>
        <span style="color:#888;">Share: 
          <i class="bi bi-facebook ms-2"></i>
          <i class="bi bi-instagram ms-2"></i>
          <i class="bi bi-twitter ms-2"></i>
          <i class="bi bi-whatsapp ms-2"></i>
        </span>
      </div>
    </div>
  </div>
</div>

<div class="container my-5">
  <!-- Tabs Navigation -->
  <ul class="nav nav-tabs justify-content-center border-0 mb-4 " style="font-size:1.2rem;">
    <li class="nav-item">
      <a class="nav-link active" data-bs-toggle="tab" href="#review" style="color:#747265; font-weight:500; border: none;">Reviews</a>
    </li>
    <li class="nav-item">
      <a class="nav-link" data-bs-toggle="tab" href="#description" style="color:#747265;border: none;">Description</a>
    </li>
    <li class="nav-item">
      <a class="nav-link" data-bs-toggle="tab" href="#additional" style="color:#747265; border: none;">Additional Information</a>
    </li>
  </ul>

  <div class="tab-content">


    <!-- Review Tab -->
    <div class="tab-pane fade show active" id="review">
      <div class="row g-4 align-items-center">
        <div class="col-md-4 text-center">
          <div style="font-size:3.2rem; font-weight:700; color:#232312;">4.8 <span style="font-size:1.1rem; color: #8f8f8f;">out of 5</span></div>
          <div class="mb-2">
            <span style="color:#ffe74b; font-size:2rem;">★★★★★</span>
          </div>
          <div style="color:#888; font-size:1rem;">(9 Reviews)</div>
        </div>
        <div class="col-md-6">
          <!-- Ratings Bar -->
          <div class="mb-2 d-flex align-items-center">
            <span class="me-2">5 Star</span>
            <div class="progress flex-grow-1" style="height:8px;">
              <div class="progress-bar bg-warning" style="width: 80%;"></div>
            </div>
          </div>
          <div class="mb-2 d-flex align-items-center">
            <span class="me-2">4 Star</span>
            <div class="progress flex-grow-1" style="height:8px;">
              <div class="progress-bar bg-warning" style="width: 60%;"></div>
            </div>
          </div>
          <div class="mb-2 d-flex align-items-center">
            <span class="me-2">3 Star</span>
            <div class="progress flex-grow-1" style="height:8px;">
              <div class="progress-bar bg-secondary bg-opacity-25" style="width: 10%;"></div>
            </div>
          </div>
          <div class="mb-2 d-flex align-items-center">
            <span class="me-2">2 Star</span>
            <div class="progress flex-grow-1" style="height:8px;">
              <div class="progress-bar bg-secondary bg-opacity-25" style="width: 8%;"></div>
            </div>
          </div>
          <div class="mb-2 d-flex align-items-center">
            <span class="me-2">1 Star</span>
            <div class="progress flex-grow-1" style="height:8px;">
              <div class="progress-bar bg-secondary bg-opacity-25" style="width: 5%;"></div>
            </div>
          </div>
        </div>
      </div>

      <!-- Review Submission -->
      <div class="mt-5 mb-4 px-lg-5" style="max-width:660px;">
        <div style="font-size:1.8rem; font-weight:400; color:#998204;" class="mb-1">Leave a Review</div>
        <form>
          <div class="mb-3">
            <label style="font-size:1.1rem; color:#5d591e;">rate us</label>
            <div>
              <span class="h4" style="color:#ccc; cursor:pointer;">★</span>
              <span class="h4" style="color:#ccc; cursor:pointer;">★</span>
              <span class="h4" style="color:#ccc; cursor:pointer;">★</span>
              <span class="h4" style="color:#ccc; cursor:pointer;">★</span>
              <span class="h4" style="color:#ccc; cursor:pointer;">★</span>
            </div>
          </div>
          <div class="mb-3">
            <textarea class="form-control" rows="2" placeholder="Write a review..." style="font-size:1rem;"></textarea>
          </div>
          <button type="submit" class="btn btn-outline-warning px-4" style="color:#998204;  font-size:1.2rem;">Submit Review</button>
        </form>
      </div>
      <div class="container my-5" style="max-width:1020px;">
  <h2 style="color:#998204; font-size:1.8rem; font-weight:450;" class="mb-4">Review List</h2>

  <!-- Review Card Start -->
  <div class="row align-items-start mb-5">
    <div class="col-2 col-md-1 d-flex justify-content-center align-items-center">
      <img src="" alt="Siyanga Majenga" class="rounded-circle border" style="width:60px; height:60px; object-fit:cover;">
    </div>
    <div class="col-7 col-md-8">
      <div style="font-weight:500; font-size:1.08rem;">Siyanga Majenga</div>
      <div style="color:#b1af91; font-size:0.98rem;">(verified)</div>
      <div class="mt-2 mb-0" style="font-weight:500; color:#444;">Good Quality Shoe!</div>
      <a href="mailto:siyanga@gmail.com" style="text-decoration:none; color:#444;">
        <div style="color:#757575; font-size:1rem;">
          I recently bought these shoes and they have lasted me this far. The in-soles were replaced with a new in-sole. No breakage. Arrived clean. No complaints from my kids. 
          second-hand price with first-hand quality.
        </div>
      </a>
      <div class="mt-2" style="color:#ffe74b; font-size:1.2rem;">★★★★☆ <span style="color:#232312; font-size:1rem; font-weight:600;">4.0</span></div>
    </div>
    <div class="col-3 col-md-3 text-end" style="color:#a2a187; font-size:1rem; font-weight:500;">
      1 month ago
    </div>
  </div>
  <!-- End Review Card -->

  <!-- Repeated each review -->
  <div class="row align-items-start mb-5">
  </div>

 


  <!-- ...more reviews as needed... -->
</div>
 </div>





    <!-- Description Tab -->
    <div class="tab-pane fade" id="description">
     <div class="p-3" style="font-size:1.15rem; color:#444;">
  <div style=" font-size:1.2rem; font-weight:400; margin-bottom:1rem; color:#444">
   Genuine leather school shoes featuring a scuff-resistant upper and a durable, flexible rubber sole. Despite being pre-owned, the shoes maintain strong structural integrity, intact stitching, and minimal tread wear. Fully cleaned, sanitized, and ready for use.
  </div>

  <div style="color: #998204; font-size:1.25rem; font-weight:600; margin:1.2rem 0 0.6rem;">
    Condition
    <div style=" font-size:1.2rem; font-weight:400; margin-bottom:1rem; color:#444">
   Excellent
  </div>

   <div style="color: #998204; font-size:1.25rem; font-weight:600; margin:1.2rem 0 0.6rem;">
    Sanitation
    <div style=" font-size:1.2rem; font-weight:400; margin-bottom:1rem; color:#444">
      Includes a Verified Digital Hygiene and Sanitation Certificate confirming compliance with sanitization regulations.
  </div>
</div>
</div>

    <!-- Additional Information Tab -->
    <div class="tab-pane fade" id="additional">
      <div class="p-3" style="font-size:1.12rem; color:#444;">
        <b style="color:#998204;">Shipping:</b> Fast shipping across South Africa.<br>
        <b style="color:#998204;">Returns:</b> 30-day hassle-free returns.<br>
        <b style="color:#998204;">Contact:</b> unishoethrift.com
      </div>
      <div class="p-3">
        <div style="color:#998204; font-size:1.3rem; font-weight:600; margin-bottom:1.2rem;">
        Certifications:
      </div>
       <div class="row align-items-center g-4">
         <div class="col-6 col-md-3 text-center">
          <img src="" alt="USDA Logo" class="img-fluid" style="max-height:80px;">
        </div>
         <div class="col-6 col-md-3 text-center">
          <img src="" alt="Butter Certification" class="img-fluid" style="max-height:80px;">
        </div>
      </div>
    </div>
    </div>

  <div class="container my-5">
  <!-- Underlined Heading -->
  <h2 class="fw-bold mb-4" style="color:#8a823b; border-bottom:3px solid #8a8452; display:inline-block; padding-bottom:6px; font-size:1.1rem;">
    More Products
  </h2>

  <!-- Images Row -->
  <div class="row justify-content-center g-4 mt-3 gap-5">
   

          <div class="col-12 col-md-6 col-lg-3 text-start mb-4">
        <img src="" alt="product2" 
             class="img-fluid mb-3" style="width:450px; height:300px; object-fit:cover; ">
        <h5 class="mb-2" style="font-family:'Inter',sans-serif; color:#474203; font-weight: 300; ">Product 2</h5>
        <!-- Stars -->
        <div class="mb-1" style="color:#FFD600; font-size:1.1rem;">
          &#9733;&#9733;&#9733;&#9733;&#9733; <span style="color:#444; font-size:1rem;">(9)</span>
        </div>
        <div class="mb-2 fw-bold" style="color:#222; font-family:'Inter',sans-serif; font-size:1.15rem;">R150.00</div>
        <button class="btn" style=" color:#474203; font-weight:500; border-radius: 0; border-color: #3A330F;">Add to Cart</button>
      </div>

          <div class="col-12 col-md-6 col-lg-3 text-start mb-4">
        <img src="" alt="product 3" 
             class="img-fluid mb-3" style="width:450px; height:300px; object-fit:cover;">
        <h5 class="mb-2" style="font-family:'Inter',sans-serif;  color:#474203; font-weight: 300;">product 3</h5>
        <div class="mb-1" style="color:#FFD600; font-size:1.1rem;">
          &#9733;&#9733;&#9733;&#9733;&#9733; <span style="color:#444; font-size:1rem;">(9)</span>
        </div>
        <div class="mb-2 fw-bold" style="color:#222; font-family:'Inter',sans-serif; font-size:1.15rem;">R196.00</div>
        <button class="btn" style="color:#474203; font-weight:500; border-radius: 0; border-color: #3A330F;">Add to Cart</button>
      </div>

       <div class="col-12 col-md-6 col-lg-3 text-start mb-4">
          <img src=""
          alt="product4"
           class="img-fluid mb-3"
          style="width:450px; height:300px; object-fit:cover;">
          <h5 class="mb-2"
          style="font-family:'Inter',sans-serif; color:#474203; font-weight:300; font-size:1.15rem;">
          product4 
           </h5>
           <div class="mb-1" style="color:#FFD600; font-size:1.1rem;">
          ★★★★★ <span style="color:#444; font-size:1rem;">(9)</span>
          </div>
          <div class="mb-2 fw-bold" style="color:#222; font-family:'Inter',sans-serif; font-size:1. 15rem;">
           R150.00
           </div>
         <button class="btn" style="color:#474203; font-weight:500; border-radius: 0; border-color: #3A330F;">
          Add to Cart
        </button>
      </div>
  </div>
</div>
</div>
</section>

<footer class="bg-black text-white pt-5 pb-4">
  <div class="container">
    <div class="row mb-4">
      <div class="col-lg-4 mb-4 mb-lg-0">
        <h4 class="mb-3" style="font-weight:700;">YourBrandName</h4>
        <p style="color:#b0b0b0; font-size:1rem;">
          Organic products | Wholesale & Retail | South Africa
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
        &copy; 2025 YourBrandName. All rights reserved.
      </div>
    </div>
  </div>
</footer>


<div class="modal fade" id="accountModal" tabindex="-1" aria-labelledby="accountModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content px-2 py-2">
      <div class="modal-body">
        <div class="row">

          <!-- Left: Log In -->
          <div class="col-md-6 p-4">
            <h5 class="mb-2" style="font-weight: 500;">Already Signed up?</h5>
            <p class="mb-4" style="font-size: 1rem;">Log in below or use a provider:</p>

              <form action="login.php" method="POST" action= "" id="loginForm" > 
              <input type="email" class="form-control mb-2" name="email" placeholder="Email" required>
              <div class="input-group mb-2">
                <input type="password" class="form-control" name="password" placeholder="Password" id="loginPassword" required>
                <button class="btn btn-outline-secondary btn-lash" type="button" id="togglePassword">
                  <i class="bi bi-eye-slash"></i>
                </button>
              </div>
            <button class="btn btn-light w-100 mb-3 btn-login" type="button">Log In</button>
          
            <div id="googleLoginBtn" class="mb-2"></div>
              </form>

          </div>

          

          <!-- Right: Sign Up -->
          <div class="col-md-6 p-4 border-start">
            <h5 class="mb-2" style="font-weight: 500;">Sign Up for Account</h5>
            <p style="font-size:1rem;">Let's get you all set up so you can start shopping without hassle.</p>

            <form action="signup.php" method="POST" id="signupForm">

              <input type="text" class="form-control mb-2" name="first_name" placeholder="First name" required>

              <input type="text" class="form-control mb-2" name="last_name"placeholder="Last name" required>

              <input type="email" class="form-control mb-2" name="email" placeholder="Email" required>

              <div class="input-group mb-2">
                <input type="password" class="form-control" name="password" placeholder="Password" id="signupPassword" minlength="8" required>

                <button class="btn btn-outline-secondary btn-lash" type="button" id="togglePasswordSignupPassword">
                  <i class="bi bi-eye-slash"></i>
                </button>
              </div>

              <div class="form-check mb-2">
                <input type="checkbox" class="form-check-input" id="agreeTerms" name= "terms_accepted" value="1" required>

                <label class="form-check-label" for="agreeTerms">I accept Uni-Shoe Thrifts (PTY) LTD Terms &amp; Conditions  <a href="index.php" target="_blank"> Learn More </a> </label>
              </div>
              <button class="btn btn-success w-100 mt-2 btn-signup" type="submit">Sign Up</button>

              <div class="text-center mt-3 mb-1" style="font-size: 1rem;">Or sign up using</div>
              <div class="d-flex justify-content-center gap-2">

                    <!-- Sign up with Google (right side) -->
                    <div class="d-flex justify-content-center gap-2">
                    <div id="googleSignupBtn"></div>
                    </div>

              </div>
            </form>

          </div>

        </div>
      </div>
    </div>
  </div>
  
  <div class="toast align-items-center text-bg-success border-0 position-fixed top-0 end-0 m-4" id="welcomeToast" role="alert" aria-live="assertive" aria-atomic="true" style="z-index: 9999;">
  <div class="d-flex">
    <div class="toast-body">
      <i class="bi bi-check-circle me-2"></i>Welcome back!
    </div>
    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
  </div>
</div>




<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="product.js"></script>
</body>
</html>
