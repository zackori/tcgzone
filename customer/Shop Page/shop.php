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
<title>TCGZONE | Shop</title>

<!-- Bootstrap 5 CSS -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">

<!-- Bootstrap Icons -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
<!-- Google Font -->
<!-- <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet"> -->
<link rel="icon" type="image/svg" href="/tcgzone/assets/logos/logo/transparent-image.png">
<!-- Site-wide CSS (same file the rest of the site uses, for shared colors/buttons) -->
<link rel="stylesheet" href="/tcgzone/assets/css/shared.css">
<!-- Shop Overview specific CSS -->
<link rel="stylesheet" href="shop.css?v=<?= filemtime(__DIR__ . '/shop.css') ?>">
<link rel="stylesheet" href="/tcgzone/customer/Shopping Cart Page/shopping-cart.css">
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


<!-- ========================= SHOP OVERVIEW CONTENT ========================= -->
<main class="shop-page">
  <div class="container-fluid px-3 px-lg-0 py-4">
    <div class="px-lg-4">


    <!-- Filter bar -->
    <div class="filter-bar" id="filter-bar">
      <select class="form-select filter-select" id="filterCategory" aria-label="Category">
        <option value="all" selected>Select Category</option>
        <option value="Pokémon">Pokémon</option>
        <option value="Magic: The Gathering">Magic: The Gathering</option>
        <option value="One Piece">One Piece</option>
      </select>

      <select class="form-select filter-select d-none" id="filterCardType" aria-label="Card Type">
        <option value="all" selected>Select Product Type</option>
      </select>

      <select class="form-select filter-select" id="filterPrice" aria-label="Price Range">
        <option value="all" selected>Select Price</option>
        <option value="0-100">Under  ₱100</option>
        <option value="100-500">₱100–₱500</option>
        <option value="500-1000">₱500–₱1,000</option>
        <option value="1000-5000">₱1,000–₱5,000</option>
        <option value="5000-50000">₱5,000–₱50,000</option>
        <option value="50000-500000">₱50,000–₱500,000</option>
        <option value="500000-9999999">₱500,000+</option>
      </select>

      <select class="form-select filter-select d-none" id="filterRarity" aria-label="Rarity">
        <option value="all" selected>Select Rarity</option>
      </select>

      <select class="form-select filter-select" id="filterCondition" aria-label="Condition">
        <option value="all" selected>Condition</option>
        <option value="mint">Mint</option>
        <option value="near-mint">Near Mint</option>
        <option value="lightly-played">Lightly Played</option>
        <option value="damaged">Damaged</option>
      </select>

      <select class="form-select filter-select" id="sortBy" aria-label="Sort by">
        <option value="relevance" selected>Sort by: Relevance</option>
        <option value="price-asc">Price: Low to High</option>
        <option value="price-desc">Price: High to Low</option>
        <option value="name-asc">Name: A–Z</option>
      </select>

      <button type="button" class="btn btn-clear-filters" id="clearFiltersBtn">Clear All</button>
    </div>
    </div>
    <!-- Active filter chips + results count -->
    <div class="results-bar px-5">
      <span class="active-filters-label d-none" id="active-filters-label">Active filters:</span>
      <div class="filter-chip-track" id="filter-chip-track">
        <!-- Chips injected by shop.js when a filter is active -->
      </div>
      <span class="results-count" id="resultsCount"><span class="results-count text-dark" id="resultsCount">0</span> Results Found</span>
    </div>

    <!-- Product grid -->
    <div class="row g-3 px-lg-4" id="product-grid">
      <!-- Cards rendered by shop.js -->
    </div>

    <p class="no-results-msg d-none" id="noResultsMsg">No cards match those filters. Try clearing a few.</p>

    <!-- Pagination: arrows + dots, fully JS-driven -->
    <div class="pagination-row" id="pagination-row">
      <button type="button" class="slider-arrow pagination-arrow" id="pagePrev" aria-label="Previous page">
        <i class="bi bi-chevron-left"></i>
      </button>
      <div class="pagination-dots" id="pagination-dots"></div>
      <button type="button" class="slider-arrow pagination-arrow" id="pageNext" aria-label="Next page">
        <i class="bi bi-chevron-right"></i>
      </button>
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
<!-- ========================= PRODUCT DETAIL MODAL ========================= -->
<div class="modal fade product-modal" id="productModal" tabindex="-1" aria-labelledby="productModalTitle" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content">
      <button type="button" class="btn-close btn-close-white modal-close-custom" data-bs-dismiss="modal" aria-label="Close"></button>

      <div class="modal-body p-0">
        <div class="row g-0">

          <div class="col-12 col-md-5 modal-image-col">
            <div class="modal-product-image" id="modal-product-image"></div>
            <span class="modal-out-of-stock-badge d-none" id="modal-out-of-stock-badge">Out of Stock</span>
          </div>

          <div class="col-12 col-md-7 modal-info-col">
            <h3 class="modal-product-title" id="productModalTitle"></h3>
            <p class="modal-product-subtitle" id="modal-product-subtitle"></p>
            <p class="modal-description" id="modal-description"></p>

            <div class="modal-price-row">
              <span class="modal-price" id="modal-price"></span>
              <span class="modal-compare-price d-none" id="modal-compare-price"></span>
            </div>

            <h4 class="modal-section-heading">Product Details</h4>
            <ul class="modal-requirements" id="modal-requirements"></ul>

            <div class="modal-actions">
              <div class="qty-control" id="modal-qty-stepper">
                <button type="button" class="qty-btn qty-decrease" aria-label="Decrease quantity">−</button>
                <span class="qty-value" id="modal-qty-value">1</span>
                <button type="button" class="qty-btn qty-increase" aria-label="Increase quantity">+</button>
              </div>
              <button type="button" class="btn btn-add-to-cart" id="modal-add-to-cart">
                <i class="bi bi-cart-plus"></i> Add To Cart
              </button>
            </div>

            <div class="modal-meta-row">
              <p class="modal-category">Category: <span id="modal-category"></span></p>
              <p class="modal-condition">Condition: <span id="modal-condition"></span></p>
            </div>
          </div>

        </div>
      </div>
    </div>
  </div>
</div>

<!-- Bootstrap Bundle JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<!-- Shop Overview JS -->
<script src="shop.js?v=<?= filemtime(__DIR__ . '/shop.js') ?>"></script>
</body>
</html>
