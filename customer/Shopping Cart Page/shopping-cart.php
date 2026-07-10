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
    <link rel="stylesheet" href="shopping-cart.css">
    <link rel="stylesheet" href="/tcgzone/assets/css/shared.css">
    <link rel="icon" type="image/svg" href="/tcgzone/assets/logos/logo/transparent-image.png">
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
                    <li><a href="/tcgzone/customer/Shop Page/shop-page.html">Shop</a></li>
                    <li>|</li>
                    <li><a href="#" class="category"><span>Pokémon</span></a></li>
                    <li><a href="#" class="category"><span>Magic</span></a></li>
                    <li><a href="#" class="category"><span>One Piece</span></a></li>
                    <li><a href="#">My Orders</a></li>
                </ul>
            </div> 
        </nav>

<main class="page-bg py-5">
  <div class="container">

    <!-- =========================================================
         VIEW 1 — SHOPPING CART
         ========================================================= -->
    <section id="cartView">
      <h1 class="text-center text-white mb-5">Shopping Cart</h1>

      <div class="row g-4 justify-content-center">
        <div class="col-lg-8">
          <div class="panel">
            <div class="table-responsive">
              <table class="table cart-table align-middle mb-0">
                <thead>
                  <tr>
                    <th>Product</th>
                    <th>Price</th>
                    <th class="text-center">Quantity</th>
                    <th class="text-end">Subtotal</th>
                    <th></th>
                  </tr>
                </thead>
                <tbody id="cartItemsBody">
                  <!-- rows injected by script.js from CART_ITEMS -->
                </tbody>
              </table>
            </div>

            <div class="d-flex justify-content-between align-items-center p-3 border-top border-secondary-subtle">
              <button class="btn btn-light rounded-pill px-4" id="returnToShopBtn">Return to shop</button>
              <button class="btn btn-light rounded-pill px-4" id="updateCartBtn">Update Cart</button>
            </div>
          </div>
        </div>

        <div class="col-lg-3">
          <div class="panel p-4">
            <h5 class="text-white mb-3">Subtotal</h5>
            <div class="d-flex justify-content-between small text-secondary mb-1">
              <span>Subtotal:</span><span id="sumSubtotal">$0.00</span>
            </div>
            <div class="d-flex justify-content-between small text-secondary mb-1">
              <span>Shipping:</span><span>Free</span>
            </div>
            <div class="d-flex justify-content-between fw-semibold text-white mb-3">
              <span>Total:</span><span id="sumTotal" class="text-success">$0.00</span>
            </div>
            <button class="btn btn-light w-100 rounded-pill py-2" id="proceedToCheckoutBtn">
              Proceed to checkout
            </button>
          </div>
        </div>
      </div>
    </section>


    <!-- =========================================================
         VIEW 2 — CHECKOUT / BILLING INFORMATION
         ========================================================= -->
    <section id="checkoutView" class="d-none">

      <div class="row g-4 justify-content-center">
        <div class="col-lg-8">
          <div class="panel p-4">
            <h4 class="text-white mb-4">Billing Information</h4>

            <form id="billingForm" class="billingForm">
              <div class="row g-3">
                <div class="col-md-6">
                  <label class="form-label small text-secondary" style="font-family: 'Figtree', sans-serif;">First name</label>
                  <input type="text" class="form-control" name="firstName" placeholder="Your first name" required>
                </div>
                <div class="col-md-6">
                  <label class="form-label small text-secondary">Last name</label>
                  <input type="text" class="form-control" name="lastName" placeholder="Your last name" required>
                </div>

                <div class="col-md-2">
                  <label class="form-label small text-secondary">Zip Code</label>
                  <input type="text" class="form-control" name="zipCode" placeholder="Zip Code" required>
                </div>

                <div class="col-md-5">
                  <label class="form-label small text-secondary">Province</label>
                  <input type="text" class="form-control" name="province" placeholder="Province" required>
                </div>

                <div class="col-md-5">
                  <label class="form-label small text-secondary">City</label>
                  <input type="text" class="form-control" name="city" placeholder="City" required>
                </div>

                <div class="col-md-12">
                  <label class="form-label small text-secondary">House/Unit No., Street</label>
                  <input type="text" class="form-control" name="city" placeholder="House/Unit No., Street" required>
                </div>

                <div class="col-md-6">
                  <label class="form-label small text-secondary">Email</label>
                  <input type="email" class="form-control" name="email" placeholder="Email Address" required>
                </div>
                <div class="col-md-6">
                  <label class="form-label small text-secondary">Phone</label>
                  <input type="tel" class="form-control" name="phone" placeholder="Phone number" required>
                </div>
              </div>

              <h5 class="text-white mt-4 mb-2">Additional Info</h5>
              <label class="form-label small text-secondary">Order Notes (Optional)</label>
              <textarea class="form-control" name="orderNotes" rows="3"
                placeholder="Notes about your order, e.g. special notes for delivery"></textarea>

              <div id="billingFormError" class="text-danger small mt-3 d-none">
                Please fill in all required fields correctly.
              </div>
            </form>

            <button class="btn btn-outline-light rounded-pill px-4 mt-4" id="backToCartBtn">
              &larr; Back to cart
            </button>
          </div>
        </div>

        <div class="col-lg-3">
          <div class="panel p-4">
            <h5 class="text-white mb-3">Order Summary</h5>
            <div id="checkoutItemsList" class="mb-3">
              <!-- injected by script.js -->
            </div>

            <div class="d-flex justify-content-between small text-secondary mb-1">
              <span>Subtotal:</span><span id="checkoutSubtotal">$0.00</span>
            </div>
            <div class="d-flex justify-content-between small text-secondary mb-1">
              <span>Shipping:</span><span>Free</span>
            </div>
            <div class="d-flex justify-content-between fw-semibold text-white mb-3">
              <span>Total:</span><span id="checkoutTotal">$0.00</span>
            </div>

            <h6 class="text-white mb-2">Payment Method</h6>
            <div class="form-check mb-3">
              <input class="form-check-input" type="radio" name="paymentMethod" id="codOption" value="cod" checked>
              <label class="form-check-label small text-secondary" for="codOption">
                Cash on Delivery
              </label>
            </div>

            <button class="btn btn-dark w-100 rounded-pill py-2 border" id="placeOrderBtn">
              Place Order
            </button>
            <div id="placeOrderStatus" class="small mt-2"></div>
          </div>
        </div>
      </div>
    </section>

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
    <script src="/tcgzone/bootstrap/bootstrap-5.3.8-dist/js/bootstrap.min.js"></script>
    <script src="/tcgzone/assets/js/shared/shared.js"></script>
    <script src="shopping-cart.js"></script>
</body>
</html>