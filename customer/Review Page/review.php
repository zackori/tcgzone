<?php
/* ============================================================
   Session check - drives the navbar below:
   - Logged out: Sign In / Account / Cart all lead to login.html
   - Logged in: Sign In is removed; Account -> account.php,
     Cart -> shopping-cart.html
   ============================================================ */
session_start();
$isLoggedIn = isset($_SESSION['user_id']);

// Same guard as account.php / shopping-cart.php: block the page
// itself for logged-out users, not just the Submit button. This
// covers every way someone might land here — the navbar link,
// a direct URL, a bookmark — not just clicking through normally.
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

<link rel="stylesheet" href="/tcgzone/bootstrap/bootstrap-5.3.8-dist/css/bootstrap.min.css">
<link rel="stylesheet" href="/tcgzone/assets/css/shared.css">
<link rel="stylesheet" href="review.css">
<link rel="icon" type="image/svg" href="/tcgzone/assets/logos/logo/transparent-image.png">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">

<title>TCGZONE | Submit Review</title>

<!-- Font Awesome -->
<link rel="stylesheet"
href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

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



<div class="container mt-5 mb-5">

    <h1>Review</h1>

    <div class="review-box">

        <h2>Leave a Review</h2>
        <p>We would love to hear about your experience!</p>

        <!-- Star Rating -->
        <div class="rating">

            <input type="radio" name="star" id="star5">
            <label for="star5">
                <img class="icon-off" src="/tcgzone/assets/logos/review/poke-close.svg" alt="Pokeclose">
                <img class="icon-on" src="/tcgzone/assets/logos/review/poke-open.svg" alt="poke-open">
            </label>

            <input type="radio" name="star" id="star4">
            <label for="star4">
                <img class="icon-off" src="/tcgzone/assets/logos/review/poke-close.svg" alt="Pokeclose">
                <img class="icon-on" src="/tcgzone/assets/logos/review/poke-open.svg" alt="poke-open">
            </label>

            <input type="radio" name="star" id="star3">
            <label for="star3">
                <img class="icon-off" src="/tcgzone/assets/logos/review/poke-close.svg" alt="Pokeclose">
                <img class="icon-on" src="/tcgzone/assets/logos/review/poke-open.svg" alt="poke-open">
            </label>

            <input type="radio" name="star" id="star2">
            <label for="star2">
                <img class="icon-off" src="/tcgzone/assets/logos/review/poke-close.svg" alt="Pokeclose">
                <img class="icon-on" src="/tcgzone/assets/logos/review/poke-open.svg" alt="poke-open">
            </label>

            <input type="radio" name="star" id="star1">
            <label for="star1">
                <img class="icon-off" src="/tcgzone/assets/logos/review/poke-close.svg" alt="Pokeclose">
                <img class="icon-on" src="/tcgzone/assets/logos/review/poke-open.svg" alt="poke-open">
            </label>
            
        </div>

        <!-- Rating Text -->
        <p id="ratingText">No rating selected</p>

        <!-- Form -->
        <form id="reviewForm" style="font-family: 'Figtree', san-serif;">

            <h3>Author Details</h3>

            <label>Name (Optional)</label>
            <input
                type="text"
                id="name"
                placeholder="Enter your name">

            <h3>Review</h3>

            <textarea
                id="review"
                placeholder="Share your experience with us."></textarea>

            <button type="submit" class="btn btn-buy-now">Submit</button>

        </form>


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
<script src="/tcgzone/assets/js/shared/shared.js"></script>
<script>
    const isLoggedIn = <?= $isLoggedIn ? 'true' : 'false' ?>;
</script>
<script src="review.js"></script>

</body>
</html>