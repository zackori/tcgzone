<?php
/* ============================================================
Session check - drives the navbar below:
- Logged out: Sign In / Account / Cart all lead to login.html
- Logged in: Sign In is removed; Account -> account.php,
    Cart -> shopping-cart.html
============================================================ */
session_start();
$isLoggedIn = isset($_SESSION['user_id']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/tcgzone/bootstrap/bootstrap-5.3.8-dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="footer.css">
    <link rel="stylesheet" href="/tcgzone/assets/css/shared.css">
    <link rel="icon" type="image/svg" href="/tcgzone/assets/logos/logo/transparent-image.png">
    <link rel="stylesheet"
    href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">


    <title>TCGZONE | Terms & Condition</title>
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


<main class="policy-page">

    <div class="policy-container">

        <h1 class="policy-title">Terms &amp; Conditions</h1>

        <div class="policy-content">

            <h3>&bull; Acceptance of Terms</h3>
            <p>
                By accessing, browsing, or using TCGZone, you acknowledge that
                you have read, understood, and agreed to comply with these
                Terms and Conditions. If you do not agree with any part of
                these terms, you must immediately discontinue the use of this
                website and its services.
            </p>

            <h3>&bull; User Responsibility</h3>
            <p>
                Users are solely responsible for maintaining the confidentiality
                of their account credentials. Any activity conducted under your
                account shall be deemed your responsibility. TCGZone shall not
                be held liable for unauthorized access resulting from your
                negligence.
            </p>

            <h3>&bull; Accuracy of Information</h3>
            <p>
                Users must provide accurate, complete, and up-to-date
                information during registration and throughout their use of the
                platform. Providing false or misleading information may result
                in the suspension or permanent termination of your account.
            </p>

            <h3>&bull; Buying and Selling Policy</h3>
            <p>
                Sellers are responsible for ensuring that product listings,
                descriptions, pricing, and images are accurate. Buyers are
                expected to carefully review product details before completing
                any transaction. TCGZone reserves the right to remove listings
                that violate marketplace standards.
            </p>

            <h3>&bull; Payments</h3>
            <p>
                All payments must be completed through the approved payment
                methods provided by the platform. Orders shall only be
                processed upon successful payment verification. Fraudulent,
                unauthorized, or suspicious transactions may be cancelled
                without prior notice.
            </p>

            <h3>&bull; Shipping and Delivery</h3>
            <p>
                Delivery schedules are subject to courier availability and
                shipping conditions. TCGZone shall not be responsible for
                delays, losses, or damages caused by third-party courier
                services after products have been dispatched.
            </p>

            <h3>&bull; Returns and Refunds</h3>
            <p>
                Refunds and returns shall only be considered for eligible
                transactions involving incorrect, defective, or damaged items.
                Claims must be submitted within seven (7) days of receiving the
                product together with sufficient proof. Requests that fail to
                meet these requirements may be denied.
            </p>

            <h3>&bull; Prohibited Activities</h3>
            <p>
                Users shall not engage in fraudulent transactions, harassment,
                identity theft, spamming, distribution of malicious software,
                unauthorized access attempts, or any activity that disrupts the
                operation of the platform. Violations may result in immediate
                account suspension or permanent banning without prior notice.
            </p>

            <h3>&bull; Intellectual Property</h3>
            <p>
                All content available on TCGZone, including but not limited to
                logos, trademarks, graphics, text, images, and website design,
                is protected by intellectual property laws. Unauthorized
                reproduction, modification, distribution, or commercial use is
                strictly prohibited without prior written consent.
            </p>

            <h3>&bull; Limitation of Liability</h3>
            <p>
                TCGZone shall not be liable for any direct, indirect,
                incidental, special, or consequential damages arising from the
                use of this platform, including but not limited to financial
                losses, interrupted transactions, data loss, or third-party
                actions beyond our reasonable control.
            </p>

            <h3>&bull; Account Suspension and Termination</h3>
            <p>
                TCGZone reserves the absolute right to suspend, restrict, or
                permanently terminate any account found to be in violation of
                these Terms and Conditions, without prior notice and at its
                sole discretion.
            </p>

            <h3>&bull; Amendments</h3>
            <p>
                TCGZone reserves the right to modify, update, or replace these
                Terms and Conditions at any time. Continued use of the platform
                following such modifications constitutes acceptance of the
                revised Terms and Conditions.
            </p>

        </div>

    </div>

</main>



        
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
                    <li><a href="/tcgzone/customer/Footer Additional Page/terms_condition.php">Terms &amp; Conditions</a></li>
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
    
    
</body>
</html>