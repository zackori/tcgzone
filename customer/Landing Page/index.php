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
    <link rel="stylesheet" href="landing-page.css">
    <link rel="stylesheet" href="/tcgzone/assets/css/shared.css">
    <link rel="icon" type="image/svg" href="/tcgzone/assets/logos/logo/transparent-image.png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">


    <title>TCGZONE | Home</title>
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

    <!--TITLE SECTION-->
    <header class="hero-section d-flex align-items-center justify-content-center text-center">
        <div class="hero-overlay">
            <!-- <img src="/tcgzone/assets/images/landing page/rayquaza.png" alt="Rayquaza"> -->
        </div>
        <div class="container hero-content position-relative">
            <h1 class="hero-title">Elevate Your <span class="text-accent">Collection</span></h1>
            <p class="hero-subtitle">Rare pulls, graded slabs, and sealed product — sourced and verified for collectors
                who don't play around.</p>
            <a href="/tcgzone/customer/Shop Page/shop.php" class="btn btn-shop-now">Shop Now</a>
        </div>
    </header>


    <!--FEATURED CARDS -->
    <section class="section-block" id="featured">
        <h2 class="section-title text-center mb-5 text-white">Featured Cards</h2>
        <div class="container featured-card px-6 py-3">

            <div class="slider-wrapper position-relative">
                <button class="slider-arrow slider-arrow-left" type="button" data-slider="featured-track" data-dir="-1"
                    aria-label="Scroll left">
                    <img src="/tcgzone/assets/logos/feaatured packs/play_arrow_filled-1.svg" alt="Button Left">
                </button>

                <div class="slider-track" id="featured-track">

                    <article class="product-card">
                        <div class="product-img-wrap">
                            <img class="product-img" src="/tcgzone/assets/images/landing page/image 3.png"
                                alt="Image 3">
                        </div>
                        <h3 class="product-title">Clefairy</h3>
                        <p class="product-desc">ME03: Perfect Order Illustration Rare, #094/088</p>
                        <a href="/tcgzone/customer/Shop Page/shop.php?product=prod-1" class="btn btn-buy-now">
                            Buy Now
                        </a>
                    </article>

                    <article class="product-card">
                        <div class="product-img-wrap">
                            <img class="product-img" src="/tcgzone/assets/images/landing page/image 2.png"
                                alt="Image 2">
                        </div>
                        <h3 class="product-title">Zekrom EX</h3>
                        <p class="product-desc">SV: Black Bolt Black White Rare, #172/086</p>
                        <a href="/tcgzone/customer/Shop Page/shop.php?product=prod-31" class="btn btn-buy-now">
                            Buy Now
                        </a>
                    </article>

                    <article class="product-card">
                        <div class="product-img-wrap">
                            <img class="product-img" src="/tcgzone/assets/images/landing page/image 4.png"
                                alt="Image 4">
                        </div>
                        <h3 class="product-title">Rayquaza VMAX</h3>
                        <p class="product-desc">SWSH07: Evolving Skies Secret Rare, #218/203</p>
                        <a href="/tcgzone/customer/Shop Page/shop.php?product=prod-2" class="btn btn-buy-now">
                            Buy Now
                        </a>
                    </article>

                    <article class="product-card">
                        <div class="product-img-wrap">
                            <img class="product-img" src="/tcgzone/assets/images/landing page/image 6.png"
                                alt="Image 6">
                        </div>
                        <h3 class="product-title">Mega Charizard X EX</h3>
                        <p class="product-desc">ME02: Phantasmal Flames Special Illustration Rare, #125/094</p>
                        <a href="/tcgzone/customer/Shop Page/shop.php?product=prod-9" class="btn btn-buy-now">
                            Buy Now
                        </a>
                    </article>

                    <article class="product-card">
                        <div class="product-img-wrap">
                            <img class="product-img" src="/tcgzone/assets/images/landing page/image 7.png"
                                alt="Image 7">
                        </div>
                        <h3 class="product-title">Umbreon ex - 161/131</h3>
                        <p class="product-desc">SV: Prismatic EvolutionsSpecial Illustration Rare, #161/131</p>
                        <a href="/tcgzone/customer/Shop Page/shop.php?product=prod-35" class="btn btn-buy-now">
                            Buy Now
                        </a>
                    </article>

                    <article class="product-card">
                        <div class="product-img-wrap">
                            <img class="product-img" src="/tcgzone/assets/images/landing page/image 8.png"
                                alt="Image 8">
                        </div>
                        <h3 class="product-title">Pikachu ex - 276/217</h3>
                        <p class="product-desc">ME: Ascended Heroes Special Illustration Rare, #276/217</p>
                        <a href="/tcgzone/customer/Shop Page/shop.php?product=prod-39" class="btn btn-buy-now">
                            Buy Now
                        </a>
                    </article>

                    <article class="product-card">
                        <div class="product-img-wrap">
                            <img class="product-img" src="/tcgzone/assets/images/landing page/image 9.png"
                                alt="Image 9">
                        </div>
                        <h3 class="product-title">Gengar VMAX</h3>
                        <p class="product-desc">SWSH08: Fusion StrikeUltra Rare, #157/264</p>
                        <a href="/tcgzone/customer/Shop Page/shop.php?product=prod-13" class="btn btn-buy-now">
                            Buy Now
                        </a>
                    </article>

                    <article class="product-card">
                        <div class="product-img-wrap">
                            <img class="product-img" src="/tcgzone/assets/images/landing page/image 10.png"
                                alt="Image 10">
                        </div>
                        <h3 class="product-title">Mega Lopunny & Jigglypuff GX (Secret)</h3>
                        <p class="product-desc">SM - Cosmic Eclipse Rainbow Rare, #261/236</p>
                        <a href="/tcgzone/customer/Shop Page/shop.php?product=prod-6" class="btn btn-buy-now">
                            Buy Now
                        </a>
                    </article>

                    <article class="product-card">
                        <div class="product-img-wrap">
                            <img class="product-img" src="/tcgzone/assets/images/landing page/image 11.png"
                                alt="Image 11">
                        </div>
                        <h3 class="product-title">Mega Greninja ex - 116/086</h3>
                        <p class="product-desc">ME04: Chaos RisingSpecial Illustration Rare, #116/086</p>
                        <a href="/tcgzone/customer/Shop Page/shop.php?product=prod-10" class="btn btn-buy-now">
                            Buy Now
                        </a>
                    </article>

                    <article class="product-card">
                        <div class="product-img-wrap">
                            <img class="product-img" src="/tcgzone/assets/images/landing page/image 12.png"
                                alt="Image 12">
                        </div>
                        <h3 class="product-title">Mew ex - 232/091</h3>
                        <p class="product-desc">SV: Paldean FatesSpecial Illustration Rare, #232/091</p>
                        <a href="/tcgzone/customer/Shop Page/shop.php?product=prod-14" class="btn btn-buy-now">
                            Buy Now
                        </a>
                    </article>

                </div>

                <button class="slider-arrow slider-arrow-right" type="button" data-slider="featured-track" data-dir="1"
                    aria-label="Scroll right">
                    <img src="/tcgzone/assets/logos/feaatured packs/play_arrow_filled.svg" alt="Button Right">
                </button>
            </div>
        </div>
    </section>

    <!--UPCOMING PACKS-->
    <!-- ========================= UPCOMING PACKS ========================= -->
    <section class="section-block section-alt text-white">
        <div class="container">
            <h2 class="section-title text-center mb-5">Upcoming Packs</h2>

            <div class="row g-4 align-items-stretch upcoming-packs-wrapper">

                <!-- Image side -->
                <div class="col-12 col-lg-6">
                    <div class="pack-slider position-relative h-100">
                        <button class="slider-arrow slider-arrow-left" type="button" id="pack-prev"
                            aria-label="Previous pack">
                            <img src="/tcgzone/assets/logos/upcoming packs/caret-left.svg" alt="Left">
                        </button>

                        <div class="pack-image-frame">
                            <div class="pack-image" id="pack-image"></div>
                        </div>

                        <button class="slider-arrow slider-arrow-right" type="button" id="pack-next"
                            aria-label="Next pack">
                            <img src="/tcgzone/assets/logos/upcoming packs/caret-right.svg" alt="Right">
                        </button>
                    </div>
                </div>

                <!-- Description side (synced via JS) -->
                <div class="col-12 col-lg-6">
                    <div class="pack-info h-100">
                        <h3 class="pack-info-title" id="pack-title">Lorem Ipsum</h3>
                        <p class="pack-info-desc" id="pack-desc">
                            Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor.
                        </p>
                        <p class="pack-info-date">Release date: <strong id="pack-date">June 28, 2026</strong></p>

                        <div class="pack-dots" id="pack-dots"></div>
                    </div>
                </div>

            </div>
        </div>
    </section>

    <!-- ========================= COLLECTIONS ========================= -->
    <section class="collection section-block">
        <div class="container">
            <h2 class="section-title text-center mb-5 text-white">Collections</h2>

            <div class="slider-wrapper position-relative collections-slider">
                <button class="slider-arrow slider-arrow-left" type="button" data-slider="collections-track"
                    data-dir="-1" aria-label="Scroll left">
                    <img src="/tcgzone/assets/logos/upcoming packs/caret-left.svg" alt="Left">
                </button>

                <div class="slider-track" id="collections-track">
                    <div class="collection-card">
                        <img class="collection-img" src="/tcgzone/assets/images/landing page/image 18.png"
                            alt="Collection 1">
                    </div>
                    <div class="collection-card">
                        <img class="collection-img" src="/tcgzone/assets/images/landing page/image  19.png"
                            alt="Collection 2">
                    </div>
                    <div class="collection-card">
                        <img class="collection-img" src="/tcgzone/assets/images/landing page/image 20.jpg"
                            alt="Collection 3">
                    </div>
                    <div class="collection-card">
                        <img class="collection-img" src="/tcgzone/assets/images/landing page/image 21.png"
                            alt="Collection 4">
                    </div>
                    <div class="collection-card">
                        <img class="collection-img" src="/tcgzone/assets/images/landing page/image 22.png"
                            alt="Collection 4">
                    </div>
                </div>

                <button class="slider-arrow slider-arrow-right" type="button" data-slider="collections-track"
                    data-dir="1" aria-label="Scroll right">
                    <img src="/tcgzone/assets/logos/upcoming packs/caret-right.svg" alt="Right">
                </button>
            </div>

            <div class="text-center mt-2">
                <!-- <button class="btn btn-buy-now">See All Collections</button> -->
                <a href="/tcgzone/customer/Shop Page/shop.php?category=Pokémon&cardType=Collections"
                    class="btn btn-buy-now">See All Collections!</a>
            </div>
        </div>
    </section>


    <!--REVIEWS-->
    <section class="section-block section-alt" id="reviews">
        <div class="container">
            <h2 class="section-title text-center mb-5 text-white">Community Reviews</h2>

            <div class="row g-4 justify-content-center">

                <div class="col-11 col-md-5 col-lg-3">
                    <div class="review-card h-100">
                        <div><img class="review-avatar" src="/tcgzone/assets/images/landing page/image 17.png"
                                alt="Image 17"></div>
                        <h3 class="review-name text-white">Slime na cute</h3>
                        <div class="review-stars">
                            <img src="/tcgzone/assets/logos/review/poke-open.svg" alt="Pokeball">
                            <img src="/tcgzone/assets/logos/review/poke-open.svg" alt="Pokeball">
                            <img src="/tcgzone/assets/logos/review/poke-open.svg" alt="Pokeball">
                            <img src="/tcgzone/assets/logos/review/poke-open.svg" alt="Pokeball">
                            <img src="/tcgzone/assets/logos/review/poke-open.svg" alt="Pokeball">
                        </div>
                        <p class="review-text">"Absolutely love this marketplace! Found a near-mint Zekrom ex from Black
                            Bolt in under 10 minutes and the price was better than everywhere else. Fast shipping and
                            the card condition was exactly as described. Already bought 3 more cards. Highly recommended
                            for serious collectors!"</p>
                    </div>
                </div>

                <div class="col-11 col-md-5 col-lg-3">
                    <div class="review-card h-100">
                        <div><img class="review-avatar" src="/tcgzone/assets/images/landing page/image 17.png" alt="17">
                        </div>
                        <h3 class="review-name text-white">Ditto</h3>
                        <div class="review-stars">
                            <img src="/tcgzone/assets/logos/review/poke-open.svg" alt="Pokeball">
                            <img src="/tcgzone/assets/logos/review/poke-open.svg" alt="Pokeball">
                            <img src="/tcgzone/assets/logos/review/poke-open.svg" alt="Pokeball">
                            <img src="/tcgzone/assets/logos/review/poke-open.svg" alt="Pokeball">
                            <img src="/tcgzone/assets/logos/review/poke-open.svg" alt="Pokeball">
                        </div>
                        <p class="review-text">"Amazing experience from start to finish! Picked up a pristine Reshiram
                            ex from White Flare without any hassle, and the pricing was surprisingly affordable. The
                            packaging was secure, shipping was quick, and the card looked flawless. Will definitely keep
                            coming back for future releases!"</p>
                    </div>
                </div>

                <div class="col-11 col-md-5 col-lg-3">
                    <div class="review-card h-100">
                        <div><img class="review-avatar" src="/tcgzone/assets/images/landing page/image 17.png" alt="17">
                        </div>
                        <h3 class="review-name text-white">Ditto ka na lang</h3>
                        <div class="review-stars">
                            <img src="/tcgzone/assets/logos/review/poke-open.svg" alt="Pokeball">
                            <img src="/tcgzone/assets/logos/review/poke-open.svg" alt="Pokeball">
                            <img src="/tcgzone/assets/logos/review/poke-open.svg" alt="Pokeball">
                            <img src="/tcgzone/assets/logos/review/poke-open.svg" alt="Pokeball">
                            <img src="/tcgzone/assets/logos/review/poke-open.svg" alt="Pokeball">
                        </div>
                        <p class="review-text">"Couldn't be happier with my purchase! Finally completed my Scarlet &
                            Violet collection thanks to the wide selection available here. Everything arrived on time,
                            carefully protected, and in excellent condition. Great service, fair prices, and an awesome
                            marketplace for Pokémon TCG fans!"</p>
                    </div>
                </div>

            </div>

            <div class="text-center mt-5">
                <a href="/tcgzone/customer/Review Page/community_reviews.php" class="btn btn-buy-now">See all
                    reviews!</a>
            </div>
        </div>
    </section>


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
    <script src="/tcgzone/bootstrap/bootstrap-5.3.8-dist/js/bootstrap.min.js"></script>
    <!-- <script src="/tcgzone/assets/js/shared/shared.js"></script> -->
    <script src="landing-page.js"></script>
</body>

</html>