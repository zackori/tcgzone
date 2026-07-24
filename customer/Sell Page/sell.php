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
<link rel="stylesheet" href="sell.css">
<link rel="icon" type="image/svg" href="/tcgzone/assets/logos/logo/transparent-image.png">

<title>TCGZONE | Sell</title>

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

<main class="page-content">
    <div class="container mt-5 mb-5">

    <h1>Sell Your Cards</h1>

    <div class="review-box">

        <h2>Sell Request Form</h2>
        <p>Submit the card details you want to sell, and our admins will review your request.</p>

        <!-- Sell request form -->
        <form id="sellRequestForm" enctype="multipart/form-data" style="font-family: 'Figtree', san-serif;">
            <h3>Card Details</h3>
            <div class="row gy-4">
                <div class="col-12 col-md-6">
                    <label for="cardName">Card Name</label>
                    <input type="text" id="cardName" name="card_name" placeholder="Enter the card name" required>
                </div>

                <div class="col-12 col-md-6">
                    <label for="setName">Set Name</label>
                    <input type="text" id="setName" name="set_name" placeholder="Enter the card set" required>
                </div>

                <div class="col-12 col-md-6">
                    <label for="category">Category</label>
                    <select id="category" name="category" required>
                        <option value="">Select category</option>
                        <option value="Pokémon">Pokémon</option>
                        <option value="Magic: The Gathering">Magic: The Gathering</option>
                        <option value="One Piece">One Piece</option>
                    </select>
                </div>

                <div class="col-12 col-md-6">
                    <label for="productType">Product Type</label>
                    <select id="productType" name="product_type" required>
                        <option value="">Select product type</option>
                        <option value="Cards">Cards</option>
                        <option value="Sealed">Sealed</option>
                        <option value="Collections">Collections</option>
                        <option value="Character">Character</option>
                        <option value="Leader">Leader</option>
                        <option value="Artifact">Artifact</option>
                        <option value="Legendary Creature">Legendary Creature</option>
                        <option value="Legendary Artifact">Legendary Artifact</option>
                        <option value="Enchantment">Enchantment</option>
                        <option value="Instant">Instant</option>
                        <option value="Creature">Creature</option>
                    </select>
                </div>

                <div class="col-12 col-md-6">
                    <label for="rarity">Rarity</label>
                    <select id="rarity" name="rarity" required>
                        <option value="">Select rarity</option>
                        <option value="Common">Common</option>
                        <option value="Uncommon">Uncommon</option>
                        <option value="Rare">Rare</option>
                        <option value="Ultra Rare">Ultra Rare</option>
                        <option value="Secret Rare">Secret Rare</option>
                        <option value="C">C</option>
                        <option value="UC">UC</option>
                        <option value="R">R</option>
                        <option value="SR">SR</option>
                        <option value="SEC">SEC</option>
                        <option value="L">L</option>
                        <option value="P">P</option>
                        <option value="SP">SP</option>
                        <option value="AA">AA</option>
                        <option value="TR">TR</option>
                        <option value="MR">MR</option>
                        <option value="M">M</option>
                        <option value="S">S</option>
                        <option value="U">U</option>
                    </select>
                </div>

                <div class="col-12 col-md-6">
                    <label for="condition">Condition</label>
                    <select id="condition" name="condition" required>
                        <option value="">Select condition</option>
                        <option value="Mint">Mint</option>
                        <option value="Near Mint">Near Mint</option>
                        <option value="Lightly Played">Lightly Played</option>
                        <option value="Damaged">Damaged</option>
                    </select>
                </div>

                <div class="col-12 col-md-6">
                    <label for="sellingPrice">Selling Price</label>
                    <input type="number" id="sellingPrice" name="selling_price" placeholder="Enter selling price" step="0.01" min="0" required>
                </div>

                <div class="col-12 col-md-6">
                    <label for="quantity">Quantity</label>
                    <input type="number" id="quantity" name="quantity" placeholder="Enter quantity" min="1" required>
                </div>

                <div class="col-12 col-md-6">
                    <label for="cardImage">Card Image</label>
                    <input type="file" id="cardImage" name="image" accept="image/*" required>
                </div>

                <div class="col-12">
                    <label for="notes">Additional Notes</label>
                    <textarea id="notes" name="notes" placeholder="Add any additional notes or card details"></textarea>
                </div>

                <div class="col-12">
                    <button type="submit" class="btn btn-buy-now">Submit Request</button>
                </div>
            </div>
        </form>

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
<script src="sell.js"></script>

</body>
</html>