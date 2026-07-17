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






        
<!--FOOTER-->
<main class="policy-page">

    <div class="policy-container">

        <h1 class="policy-title">About Us</h1>

        <div class="policy-content">

            <h3>Who We Are</h3>
            <p>
                TCGZone is an online marketplace dedicated to trading card game
                enthusiasts. We provide a secure platform where collectors,
                players, and hobbyists can buy, sell, and explore trading cards
                from popular games such as Pokémon, Magic: The Gathering, and
                One Piece.
            </p>

            <h3>Our Mission</h3>
            <p>
                Our mission is to create a trusted and accessible marketplace
                that connects trading card enthusiasts while promoting fair,
                transparent, and secure transactions. We aim to make collecting
                and trading cards simple, enjoyable, and reliable for everyone.
            </p>

            <h3>Our Vision</h3>
            <p>
                We envision TCGZone becoming one of the leading online trading
                card marketplaces by building a strong community where collectors
                and players can confidently expand their collections and share
                their passion for trading card games.
            </p>

            <h3>What We Offer</h3>
            <p>
                TCGZone provides a wide selection of trading cards, a convenient
                shopping experience, secure transactions, and an easy-to-use
                platform for both buyers and sellers. Our goal is to help users
                find the cards they need while ensuring a safe and enjoyable
                marketplace experience.
            </p>

            <h3>Why Choose TCGZone?</h3>
            <p>
                • Secure and reliable transactions.<br>
                • User-friendly shopping experience.<br>
                • Wide variety of trading card products.<br>
                • Transparent buying and selling process.<br>
                • Dedicated support for our community.
            </p>

            <h3>Our Commitment</h3>
            <p>
                We are committed to continuously improving our platform through
                innovation, security, and excellent customer service. TCGZone
                strives to provide a marketplace where every collector and player
                feels confident, valued, and connected to the trading card
                community.
            </p>

        </div>

    </div>

</main>
<footer class="site-footer">
        <div class="footer-main">
            <div class="footer-col footer-brand">
                <h2 class="footer-logo"><a href="/tcgzone/customer/Landing Page/index.html">tcgzone</a></h2>
                <p class="footer-tagline">Elevate your collection.</p>
            </div>

            <div class="footer-col footer-links">
                <h3 class="footer-heading">Quick Links</h3>
                <ul>
                    <li><a href="/tcgzone/customer/Shop Page/shop-page.html">Buy Card</a></li>
                    <li><a href="/tcgzone/customer/Footer Additional Page/about_us.html">About us</a></li>
                    <li><a href="/tcgzone/customer/Footer Additional Page/contacts.html">Contact</a></li>
                </ul>
            </div>

            <div class="footer-col footer-links">
                <h3 class="footer-heading">Policy</h3>
                <ul>
                    <li><a href="/tcgzone/customer/Footer Additional Page/terms_condition.html">Terms &amp; Conditions</a></li>
                    <li><a href="/tcgzone/customer/Footer Additional Page/privacy.html">Privacy Policy</a></li>
                </ul>
            </div>

            <div class="footer-col footer-socials">
                <h3 class="footer-heading">Socials</h3>
                <div class="social-icons">
                    <div class="link-container">
                        <a href="https://www.x.com/"><img src="/tcgzone/assets/logos/footer/x-logo.svg" alt="X"></a>
                    </div>
                    <div class="link-container">
                        <a href="https://www.facebook.com/"><img src="/tcgzone/assets/logos/footer/facebook-logo.svg" alt="X"></a>
                    </div>
                    <div class="link-container">
                        <a href="https://www.instagram.com/" target="_blank"><img src="/tcgzone/assets/logos/footer/instagram-logo.svg" alt="X"></a>
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