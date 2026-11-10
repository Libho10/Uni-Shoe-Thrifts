<?php
session_start();

$showWelcome = false;
$welcomeMessage = "";

$isLoggedIn = isset($_SESSION["logged_in"]) && $_SESSION["logged_in"] === true;

$firstName = $_SESSION["first_name"] ?? "";
$lastName = $_SESSION["last_name"] ?? "";
$email = $_SESSION["email"] ?? "";

if (!empty($_SESSION["welcome_message"])) {
    $showWelcome = true;
    $welcomeMessage = $_SESSION["welcome_message"];
    unset($_SESSION["welcome_message"]);
}

$cartItemCount = 0;
?>

  

<!DOCTYPE html>
<html lang = en>
    <head>
        <meta charset ="UTF-8">
        <meta name = "viewport" content="width=device-width, initial-scale=1.0"> 
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">

        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.12.0/font/bootstrap-icons.css">

        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.2/font/bootstrap-icons.css">
 

          <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
           <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">

     <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Unbounded:wght@200..900&display=swap" rel="stylesheet">

        <link rel="stylesheet" href="index.css"> 

<!-- Google Identity Services -->
<script src="https://accounts.google.com/gsi/client" async defer></script> <!-- google identity serv-->
<meta name="google-signin-client_id" content="694501172256-5lgva3adqcs6df3ho75907qim3ibi4vu.apps.googleusercontent.com"> <!-- meta tag -->
<!-- -->
  

        <title> homepage</title>
    
     </head>
        <body>
           
         <div class ="main-container">
            <div class="scroll-container">
             <div class="text-row">
                <div class="infinite-scroll">
                    <div class="text-scroll">New sport shoes 50% off this summers school year</div>
                    <div class="text-scroll">New buyers get up to 20% off first purchase</div>
                    <div class="text-scroll">Bundle purchases this, premium quality</div>
                    <div class="text-scroll">Santisation quality control certifcations below</div>
                    <div class="text-scroll">More amazing stuff coming soon!!</div>
                    <div class="text-scroll">Cannot wait to see you all there!</div>
              </div>
            </div>
            </div>
         </div>
        
        
            <nav class="navbar navbar-expand-lg bg-body-tertiary">
                <div class="container-fluid mx-5">
                        <a class="navbar-brand" href="#">
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
                                        <li><a class="dropdown-item" href="products.php">Tennis</a></li>
                                        <li><a class="dropdown-item" href="products.php">Soccer</a></li>
                                        <li><a class="dropdown-item" href="products.php">Netball</a></li>
                                        <li><a class="dropdown-item" href="products.php">Rugby</a></li>
                                        <li><hr class="dropdown-divider"></li>
                                        <li><a class="dropdown-item" href="products.php">See All</a></li>
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
                                  <?php if (!$isLoggedIn): ?>
                                      <a
                                          href="#"
                                          id="openAccountLink"
                                          data-bs-toggle="modal"
                                          data-bs-target="#accountModal"
                                      >
                                          <i class="bi bi-person" style="font-size:1.6rem;"></i>
                                      </a>
                                  <?php else: ?>
                                      <a
                                          href="account.php"
                                          id="openAccountLink"
                                      >
                                          <i class="bi bi-person" style="font-size:1.6rem;"></i>
                                      </a>
                                  <?php endif; ?>
                              </li>

                                <li class="nav-item">
                                   <button class="btn btn-outline-success btn-contact-us" type="submit">Contact Us</button>
                                </li>
                            </ul>
                    </div>
                </div>
            </nav>



<main role="main" class="inner-cover">
    <div class =cover-page >
<h1 class="cover-heading">Only the Best Quality <span class ="sc1">School </span> and <span class ="sc2"> Sport</span> Shoes for your Kids</h1>
<p class="lead">From the classroom to the sports field, shop at our trusted pre-loved brands in South Africa's fastest growing peer network </p>

<div class ="statistics">
    <div class = "statistics-items">
        <div class = first-stat> 
            <h1 class = "stat-num">1000+</h1>
            <p class = "stat-desc"> Growing customers</p>
        </div>

        <div class = first-stat> 
            <h1 class = "stat-num">30+</h1>
            <p class = "stat-desc"> In-store visits</p>
        </div>
        <div class = first-stat> 
            <h1 class = "stat-num">10+</h1>
            <p class = "stat-desc"> Different Brands</p>
        </div>
        
    </div>
 </div>
<p class="lead">
    <button class="btn btn-outline-success btn-shopnow" type="submit" ><a href="products.php"> Shop Now</button>
</p>
 
    </div>

    
</main>

  <div class="main-image">
      <img src = "main-image.png" alt ="main-image"> 
    </div> 

        <section style="height: auto; min-height: 100vh;">
    
  <section class="py-5">
  <div class="container px-4">
    <h2 class="mb-4 text-center category-heading" style=" font-family: 'Unbounded', sans-serif; 
         
          font-weight: 300;
          color: #474203;
          font-size: 2rem;">Choose your selection</h2>
    <div class="row justify-content-center ">

      <div class="col-6 col-md-3 text-center mb-4">
         <a href="products.php" style="text-decoration:none; color:inherit;">
        <img src="shoe-option.png" alt="Butters" class="img-fluid category-img" />
        <p class="mt-2 mb-0" style="font-family: 'Inter', sans-serif; font-weight: 300;">School</p>
        </a>
      </div>

      <div class="col-6 col-md-3 text-center mb-4">
          <a href="" style="text-decoration:none; color:inherit;">
        <img src="sport-shoes-option.png" alt="Soaps" class="img-fluid category-img" />
        <p class="mt-2 mb-0" style="font-family: 'Inter', sans-serif; font-weight: 300;">Sports</p>
        </a>
      </div>


    </div>
  </div>
</section>

<section id ="about" class="organic-hero-section" style="background-color: #fffce3; width: 100vw; position: relative; left: 50%; right: 50%; margin-left: -50vw; margin-right: -50vw;">
  <div class="container py-5 px-5">
    <div class="row align-items-center">
      <!-- Left column: text content (centered like Flash Sale) -->
           <div class="col-lg-6 text-center text-lg-end">
        <img src="washed-shoes.png" 
             alt="Hands holding organic soaps" 
             style="max-width:550px; width:100%; height:400px;">
        </div>
      <!-- Right column: product image -->
       <div class="col-lg-6 mb-4 text-center text-lg-start ">
        <p class="mb-2" style="font-family:sans-serif; font-size:1.3rem; color:#3A330F; font-weight:500;">
          Welcome to our Quality Second-hand Store
        </p>
        <h2 style="
          font-family:'Inter',sans-serif;
          color:#3A330F;
          font-style:italic;
          font-weight:700;
          font-size:3.8rem;
          line-height:1.15;
          margin-bottom:1.5rem;
        ">
          Our Products Strictly<br>
          Comply with<br>
          Sanitisation Regulations 
        </h2>
        <p class="mb-4" style="font-family: -apple-system, BlinkMacSystemFont, sans-serif; color:#666; font-size:1.1rem; font-weight:400; max-width:450px;">
          More writing explaining our certifications<br>
          and who we are certified by
        </p>
        <a href="#about" class="btn px-4 py-2 fw-semibold" 
           style="background-color:#474203; border:none; border-radius: 10px; color:white; font-size:1.1rem;">
          More About Us
        </a>
      </div>
    </div>
  </div>
</section>

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


 <?php if (!$isLoggedIn): ?> 
 <div class="modal fade" id="accountModal" tabindex="-1"         
    aria-labelledby="accountModalLabel" aria-hidden="true">
   <div class="modal-dialog modal-dialog-centered modal-lg">

    <div class="modal-content px-2 py-2">
      <div class="modal-body">
        <div class="row">

          <!-- Left: Log In -->
          <div class="col-md-6 p-4">
              <h5 class="mb-2" style="font-weight: 500;">Already Signed up?</h5>
              <p class="mb-4" style="font-size: 1rem;">Log in below or use a provider:</p>

              <form id="loginForm" method="POST" action="login.php">

                  <input
                      type="email"
                      class="form-control mb-2"
                      name="email"
                      id="loginEmail"
                      placeholder="Email"
                      required
                  >

                  <div class="input-group mb-2">
                      <input
                          type="password"
                          class="form-control"
                          name="password"
                          id="loginPassword"
                          placeholder="Password"
                          required
                      >
                      <button
                          class="btn btn-outline-secondary btn-lash"
                          type="button"
                          id="togglePassword"
                      >
                          <i class="bi bi-eye-slash"></i>
                      </button>
                  </div>

                  <button
                      class="btn btn-light w-100 mb-3 btn-login"
                      type="submit"
                  >
                      Log In
                  </button>

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
    
 </div>

 <?php endif; ?>



      <div
          id="welcomeToast"
          class="toast align-items-center text-white bg-success border-0 position-fixed"
          style="top:20px; right:20px; z-index:1090;"
          role="alert"
      >
          <div class="d-flex">
              <div class="toast-body">
                  <?= htmlspecialchars($welcomeMessage) ?>
              </div>
              <button
                  type="button"
                  class="btn-close btn-close-white me-2 m-auto"
                  data-bs-dismiss="toast"
              ></button>
          </div>
      </div>
      
            
        </body>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
        <script src="index.js"></script>
        
        <?php if ($showWelcome): ?>
           <script>
          const welcomeToast = new bootstrap.Toast(document.getElementById("welcomeToast"));
          welcomeToast.show();
          </script>
          <?php endif; ?>
</html>
