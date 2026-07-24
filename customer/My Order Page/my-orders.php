<?php
/* ============================================================
   Session check - same pattern as shopping-cart.php / account.php
   ============================================================ */
session_start();
$isLoggedIn = isset($_SESSION['user_id']);

if (!$isLoggedIn) {
    header("Location: /tcgzone/customer/Login Page/login.html");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
    <link rel="stylesheet" href="/tcgzone/bootstrap/bootstrap-5.3.8-dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="my-orders.css">
    <link rel="stylesheet" href="/tcgzone/assets/css/shared.css">
    <link rel="icon" type="image/svg" href="/tcgzone/assets/logos/logo/transparent-image.png">
    <title>My Orders — TCGZONE</title>
</head>
<body>
        <!-- NAVIGATION BAR-->
    <nav class="navbar">
        <div class="nav-top">
            <div class="logo col-auto">
                <h1><a href="/tcgzone/customer/Landing Page/index.php">tcgzone</a></h1>
            </div>
            <div class="search col-11 col-sm-6 col-md-5">
                <input class="search-input" type="text">
                <img src="/tcgzone/assets/logos/navbar/magnifying-glass.svg" alt="Search Icon">
            </div>
            <ul class="nav-links col-auto">
                <?php if (!$isLoggedIn): ?>
                    <li> <a href="/tcgzone/customer/Login Page/login.html">Sign In</a></li>
                <?php endif; ?>
                <?php if ($isLoggedIn): ?>
                    <li> <a href="/tcgzone/customer/Sell Page/sell.php">Sell</a></li>
                <?php endif; ?>
                <div class="btn-logo">
                    <li><a
                            href="<?= $isLoggedIn ? '/tcgzone/customer/Account/account.php' : '/tcgzone/customer/Login Page/login.html' ?>"><img
                                src="/tcgzone/assets/logos/navbar/user.svg" alt="Account" class="nav-icon"></a></li>
                </div>
                <div class="btn-logo">
                    <li><a
                            href="<?= $isLoggedIn ? '/tcgzone/customer/Shopping Cart Page/shopping-cart.php' : '/tcgzone/customer/Login Page/login.php' ?>">
                            <img src="/tcgzone/assets/logos/navbar/shopping-cart.svg" class="nav-icon"
                                alt="Shopping Cart"></a></li>
                </div>
            </ul>
        </div>

        <div class="nav-bottom">
            <ul class="sub-links">
                <li><a href="/tcgzone/customer/Shop Page/shop.php">Shop</a></li>
                <li>|</li>
                <li><a href="/tcgzone/customer/Shop Page/shop.php?category=Pokémon"
                        class="category"><span>Pokémon</span></a></li>
                <li><a href="/tcgzone/customer/Shop Page/shop.php?category=Magic: The Gathering"
                        class="category"><span>Magic</span></a></li>
                <li><a href="/tcgzone/customer/Shop Page/shop.php?category=One Piece" class="category"><span>One
                            Piece</span></a></li>
                <li><a
                        href="<?= $isLoggedIn ? '/tcgzone/customer/My Order Page/my-orders.php' : '/tcgzone/customer/Login Page/login.html' ?>">My
                        Orders</a></li>
            </ul>
        </div>
    </nav>


<main class="my-orders-page py-5">
  <div class="container">

    <h1 class="text-center text-white mb-4">My Orders</h1>

    <div class="orders-panel">

      <div class="orders-tabs">
        <button type="button" class="orders-tab active" id="ongoingTab">Ongoing</button>
        <button type="button" class="orders-tab" id="completedTab">Completed</button>
      </div>

      <div id="ordersList">
        <!-- Order cards injected by my-orders.js -->
      </div>

      <p class="no-orders-msg d-none" id="noOrdersMsg">No orders here yet.</p>

    </div>

  </div>
</main>

<!-- Cancel reason modal -->
<div class="cancel-modal-overlay d-none" id="cancelModal">
  <div class="cancel-modal">
    <h2>Request Cancellation</h2>
    <p class="cancel-modal-sub">Select a reason for cancelling order <strong id="cancelModalOrderId"></strong></p>

    <div class="cancel-reasons" id="cancelReasons">
      <button type="button" class="cancel-reason-btn" data-reason="Better Price Elsewhere">Better Price Elsewhere</button>
      <button type="button" class="cancel-reason-btn" data-reason="Unforeseen Financial Circumstances">Unforeseen Financial Circumstances</button>
      <button type="button" class="cancel-reason-btn" data-reason="Emergency/Unexpected Changes">Emergency / Unexpected Changes</button>
    </div>

    <p class="cancel-modal-msg d-none" id="cancelModalMsg"></p>

    <div class="cancel-modal-actions">
      <button type="button" class="cancel-modal-close" id="cancelModalClose">Close</button>
    </div>
  </div>
</div>

 <!--FOOTER-->
    <footer class="site-footer">
        <div class="footer-main">
            <div class="footer-col footer-brand">
                <h2 class="footer-logo"><a href="/tcgzone/customer/Landing Page/index.php">tcgzone</a></h2>
                <p class="footer-tagline">Elevate your collection.</p>
            </div>

            <div class="footer-col footer-links">
                <h3 class="footer-heading">Quick Links</h3>
                <ul>
                    <li><a href="/tcgzone/customer/Shop Page/shop.php">Buy Card</a></li>
                    <li><a href="/tcgzone/customer/Footer Additional Page/about_us.php">About us</a></li>
                    <li><a href="/tcgzone/customer/Footer Additional Page/contacts.php">Contact</a></li>
                </ul>
            </div>

            <div class="footer-col footer-links">
                <h3 class="footer-heading">Policy</h3>
                <ul>
                    <li><a href="/tcgzone/customer/Footer Additional Page/terms_condition.php">Terms &amp;
                            Conditions</a></li>
                    <li><a href="/tcgzone/customer/Footer Additional Page/privacy.php">Privacy Policy</a></li>
                </ul>
            </div>

            <div class="footer-col footer-socials">
                <h3 class="footer-heading">Socials</h3>
                <!-- Social -->
                <div class="footer-column">
                    <div class="social-icons">

                        <a href="https://www.facebook.com/" aria-label="Facebook">

                            <i class="fab fa-facebook-f"></i>

                        </a>

                        <a href="https://www.instagram.com/" aria-label="Instagram">

                            <i class="fab fa-instagram"></i>

                        </a>

                        <a href="https://www.x.com/" aria-label="X">

                            <i class="fab fa-x-twitter"></i>

                        </a>

                    </div>

                </div>
            </div>
        </div>

        <div class="footer-bottom">
            <p>&copy; 2026 tcgzone - All Rights Reserved</p>
        </div>
    </footer>

<!-- JavaScript -->
<script src="/tcgzone/bootstrap/bootstrap-5.3.8-dist/js/bootstrap.min.js"></script>
<script src="my-orders.js"></script>

</body>
</html>
