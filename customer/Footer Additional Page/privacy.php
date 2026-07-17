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


    <title>TCGZONE</title>
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
                    <div class="btn-logo">
                        <li><a href="<?= $isLoggedIn ? '/tcgzone/customer/Account/account.php' : '/tcgzone/customer/Login Page/login.html' ?>"><img src="/tcgzone/assets/logos/navbar/user.svg" alt="Account" class="nav-icon"></a></li>
                    </div>
                    <div class="btn-logo">
                        <li><a href="<?= $isLoggedIn ? '/tcgzone/customer/Shopping Cart Page/shopping-cart.php' : '/tcgzone/customer/Login Page/login.php' ?>"> <img src="/tcgzone/assets/logos/navbar/shopping-cart.svg" class="nav-icon" alt="Shopping Cart"></a></li>
                    </div>
                </ul>
            </div>
            
            <div class="nav-bottom">
                <ul class="sub-links">
                    <li><a href="/tcgzone/customer/Shop Page/shop.php">Shop</a></li>
                    <li>|</li>
                    <li><a href="#" class="category"><span>Pokémon</span></a></li>
                    <li><a href="#" class="category"><span>Magic</span></a></li>
                    <li><a href="#" class="category"><span>One Piece</span></a></li>
                    <li><a href="#">My Orders</a></li>
                </ul>
            </div> 
        </nav>


        <main class="policy-page">

    <div class="policy-container">

        <h1 class="policy-title">Privacy Policy</h1>

        <div class="policy-content">

            <h3>&bull; Information We Collect</h3>
            <p>
                TCGZone collects personal information that you voluntarily
                provide when creating an account, purchasing products,
                selling trading cards, or contacting customer support.
                This information may include your name, email address,
                phone number, shipping address, and payment details.
            </p>

            <h3>&bull; How We Use Your Information</h3>
            <p>
                We use your information to process orders, manage user
                accounts, improve our services, respond to inquiries,
                provide customer support, and send important updates
                regarding your transactions.
            </p>

            <h3>&bull; Protection of Personal Information</h3>
            <p>
                TCGZone takes appropriate security measures to protect your
                personal information against unauthorized access,
                disclosure, alteration, or destruction. While we strive
                to maintain a secure platform, no online system is
                completely risk-free.
            </p>

            <h3>&bull; Sharing of Information</h3>
            <p>
                We do not sell, rent, or trade your personal information.
                Information may only be shared with trusted service
                providers involved in payment processing, shipping
                services, or when required by law.
            </p>

            <h3>&bull; Cookies</h3>
            <p>
                Our website uses cookies to improve browsing experience,
                remember user preferences, and analyze website traffic.
                You may disable cookies through your browser settings,
                although some website features may not function properly.
            </p>

            <h3>&bull; Your Rights</h3>
            <p>
                Users have the right to review, update, or request the
                deletion of their personal information, subject to
                applicable laws and our operational requirements.
            </p>

            <h3>&bull; Third-Party Services</h3>
            <p>
                TCGZone may contain links to third-party websites or use
                external services. We are not responsible for the privacy
                practices or content of these third-party platforms.
            </p>

            <h3>&bull; Changes to This Policy</h3>
            <p>
                We reserve the right to update this Privacy Policy at any
                time. Changes become effective immediately after being
                posted on this page. Continued use of the website
                indicates your acceptance of any revisions.
            </p>

            <h3>&bull; Contact Us</h3>
            <p>
                If you have any questions regarding this Privacy Policy
                or how your information is handled, please contact our
                support team through the Contact Us page.
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