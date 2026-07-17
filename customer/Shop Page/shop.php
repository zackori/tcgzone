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
<title>Shop Overview — tcg.name</title>

<!-- Bootstrap 5 CSS -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<!-- Bootstrap Icons -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
<!-- Google Font -->
<!-- <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet"> -->
<link rel="icon" type="image/svg" href="/tcgzone/assets/logos/logo/transparent-image.png">
<!-- Site-wide CSS (same file the rest of the site uses, for shared colors/buttons) -->
<link rel="stylesheet" href="/tcgzone/assets/css/shared.css">
<!-- Shop Overview specific CSS -->
<link rel="stylesheet" href="shop.css">
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
                    <li><a href="/tcgzone/customer/Shop Page/shop-page.html">Shop</a></li>
                    <li>|</li>
                    <li><a href="#" class="category"><span>Pokémon</span></a></li>
                    <li><a href="#" class="category"><span>Magic</span></a></li>
                    <li><a href="#" class="category"><span>One Piece</span></a></li>
                    <li><a href="#">My Orders</a></li>
                </ul>
            </div> 
        </nav>


<!-- ========================= SHOP OVERVIEW CONTENT ========================= -->
<main class="shop-page">
  <div class="container-fluid px-3 px-lg-0 py-4">
    <div class="px-lg-4">

    <!-- Promo banner -->
    <!--<div class="shop-banner mb-4">
      <div class="shop-banner-burst"></div>
      <div class="shop-banner-content">
        <span class="banner-eyebrow">Mega Evolution</span>
        <h2 class="banner-title">Perfect<br>Order</h2>
      </div>
    </div>-->

    <!-- Filter bar -->
    <div class="filter-bar" id="filter-bar">
      <select class="form-select filter-select" id="filterCategory" aria-label="Category">
        <option value="all" selected>Select Category</option>
        <option value="Pokémon">Pokémon</option>
        <option value="Magic">Magic</option>
        <option value="One Piece">One Piece</option>
      </select>

      <select class="form-select filter-select" id="filterCardType" aria-label="Card Type">
        <option value="all" selected>Select Card Type</option>
        <option value="singles">Singles</option>
        <option value="sealed">Sealed Product</option>
        <option value="accessories">Accessories</option>
      </select>

      <select class="form-select filter-select" id="filterPrice" aria-label="Price Range">
        <option value="all" selected>Select Price</option>
        <option value="0-25">Under $25</option>
        <option value="25-100">$25 – $100</option>
        <option value="100-500">$100 – $500</option>
        <option value="500-99999">$500+</option>
      </select>

      <select class="form-select filter-select" id="filterRarity" aria-label="Rarity">
        <option value="all" selected>Select Rarity</option>
        <option value="common">Common</option>
        <option value="uncommon">Uncommon</option>
        <option value="rare">Rare</option>
        <option value="ultra-rare">Ultra Rare</option>
        <option value="secret-rare">Secret Rare</option>
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
                <h2 class="footer-logo"><a href="/tcgzone/customer/Landing Page/index.html">tcgzone</a></h2>
                <p class="footer-tagline">Elevate your collection.</p>
            </div>

            <div class="footer-col footer-links">
                <h3 class="footer-heading">Quick Links</h3>
                <ul>
                    <li><a href="/tcgzone/customer/Shop Page/shop-page.html">Buy Card</a></li>
                    <li><a href="#">About us</a></li>
                    <li><a href="#">Contact</a></li>
                </ul>
            </div>

            <div class="footer-col footer-links">
                <h3 class="footer-heading">Policy</h3>
                <ul>
                    <li><a href="#">Terms &amp; Conditions</a></li>
                    <li><a href="#">Privacy Policy</a></li>
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
<!-- ========================= PRODUCT DETAIL MODAL ========================= -->
<div class="modal fade product-modal" id="productModal" tabindex="-1" aria-labelledby="productModalTitle" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content">
      <button type="button" class="btn-close btn-close-white modal-close-custom" data-bs-dismiss="modal" aria-label="Close"></button>

      <div class="modal-body p-0">
        <div class="row g-0">

          <div class="col-12 col-md-5 modal-image-col">
            <div class="modal-product-image" id="modal-product-image"></div>
            <span class="modal-sold-out-badge d-none" id="modal-sold-out-badge">Out of Stock</span>
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
              <div class="qty-stepper" id="modal-qty-stepper">
                <button type="button" class="qty-btn qty-decrease" aria-label="Decrease quantity">−</button>
                <span class="qty-value" id="modal-qty-value">1</span>
                <button type="button" class="qty-btn qty-increase" aria-label="Increase quantity">+</button>
              </div>
              <button type="button" class="btn btn-add-to-cart" id="modal-add-to-cart">
                <i class="bi bi-cart-plus"></i> Add To Cart
              </button>
            </div>

            <p class="modal-category">Category: <span id="modal-category"></span></p>
          </div>

        </div>
      </div>
    </div>
  </div>
</div>

<!-- Bootstrap Bundle JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<!-- Shop Overview JS -->
<script src="shop.js"></script>
</body>
</html>