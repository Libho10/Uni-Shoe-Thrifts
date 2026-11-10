<?php
session_start();

// If the user is not logged in, send them back to the homepage
if (!isset($_SESSION["logged_in"]) || $_SESSION["logged_in"] !== true) {
    header("Location: index.php");
    exit();
}
$isLoggedIn = isset($_SESSION["logged_in"]) && $_SESSION["logged_in"] === true;
$firstName = $_SESSION["first_name"] ?? "";
$lastName = $_SESSION["last_name"] ?? "";
$email = $_SESSION["email"] ?? "";
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Account | UTH Website</title>

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
                                <li class="nav-item">
                                    <a class="navbar-brand" href="cart.php">
                                    <img src="cart.svg" alt="Logo" width="20" height="20">
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

    <section class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-10">

                <h2 class="mb-4">My Account</h2>

                <p class="text-muted">
                    Welcome back,
                    <?= htmlspecialchars($firstName) ?>
                    <?= htmlspecialchars($lastName) ?>
                </p>

                <ul class="nav nav-tabs mb-4" id="accountTabs" role="tablist">

                    <li class="nav-item" role="presentation">
                        <button
                            class="nav-link active"
                            id="personal-details-tab"
                            data-bs-toggle="tab"
                            data-bs-target="#personal-details"
                            type="button"
                            role="tab"
                            aria-controls="personal-details"
                            aria-selected="true"
                        >
                            Personal Details
                        </button>
                    </li>

                    <li class="nav-item" role="presentation">
                        <button
                            class="nav-link"
                            id="address-book-tab"
                            data-bs-toggle="tab"
                            data-bs-target="#address-book"
                            type="button"
                            role="tab"
                            aria-controls="address-book"
                            aria-selected="false"
                        >
                            Address Book
                        </button>
                    </li>

                    <li class="nav-item" role="presentation">
                        <button
                            class="nav-link"
                            id="logout-tab"
                            data-bs-toggle="tab"
                            data-bs-target="#logout"
                            type="button"
                            role="tab"
                            aria-controls="logout"
                            aria-selected="false"
                        >
                            Logout
                        </button>
                    </li>

                </ul>

                <div class="tab-content">

                    <!-- Personal Details -->
                    <div
                        class="tab-pane fade show active"
                        id="personal-details"
                        role="tabpanel"
                        aria-labelledby="personal-details-tab"
                    >
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title mb-3">Personal Details</h5>

                                <form id="personalDetailsForm">
                                    <div class="row">

                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">First Name</label>
                                            <input
                                                type="text"
                                                class="form-control"
                                                id="firstName"
                                                name="first_name"
                                                value="<?= htmlspecialchars($firstName) ?>"
                                                required
                                            >
                                        </div>

                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Last Name</label>
                                            <input
                                                type="text"
                                                class="form-control"
                                                id="lastName"
                                                name="last_name"
                                                value="<?= htmlspecialchars($lastName) ?>"
                                                required
                                            >
                                        </div>

                                        <div class="col-12 mb-3">
                                            <label class="form-label">Email Address</label>
                                            <input
                                                type="email"
                                                class="form-control"
                                                id="email"
                                                name="email"
                                                value="<?= htmlspecialchars($email) ?>"
                                                readonly
                                            >
                                        </div>

                                    </div>

                                    <button type="submit" class="btn btn-primary">
                                        Save Changes
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- Address Book -->
                    <div
                        class="tab-pane fade"
                        id="address-book"
                        role="tabpanel"
                        aria-labelledby="address-book-tab"
                    >
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title mb-3">Address Book</h5>

                                <form id="addressForm">
                                    <div class="row">

                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Street Address</label>
                                            <input
                                                type="text"
                                                class="form-control"
                                                name="street_address"
                                                placeholder="Enter street address"
                                            >
                                        </div>

                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Suburb</label>
                                            <input
                                                type="text"
                                                class="form-control"
                                                name="suburb"
                                                placeholder="Enter suburb"
                                            >
                                        </div>

                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">City</label>
                                            <input
                                                type="text"
                                                class="form-control"
                                                name="city"
                                                placeholder="Enter city"
                                            >
                                        </div>

                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Postal Code</label>
                                            <input
                                                type="text"
                                                class="form-control"
                                                name="postal_code"
                                                placeholder="Enter postal code"
                                            >
                                        </div>

                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Province</label>
                                            <input
                                                type="text"
                                                class="form-control"
                                                name="province"
                                                placeholder="Enter province"
                                            >
                                        </div>

                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Country</label>
                                            <input
                                                type="text"
                                                class="form-control"
                                                name="country"
                                                value="South Africa"
                                            >
                                        </div>

                                    </div>

                                    <button type="submit" class="btn btn-primary">
                                        Save Address
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- Logout -->
                    <div
                        class="tab-pane fade"
                        id="logout"
                        role="tabpanel"
                        aria-labelledby="logout-tab"
                    >
                        <div class="card">
                            <div class="card-body text-center">
                                <h5 class="card-title mb-3">Logout</h5>

                                <p class="text-muted mb-4">
                                    Are you sure you want to log out of your account?
                                </p>

                                <a href="logout.php" class="btn btn-danger">
                                    Log Out
                                </a>
                            </div>
                        </div>
                    </div>

                </div>

            </div>
        </div>
    </section>

     <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
        <script src="index.js"></script>
        

</body>
</html>