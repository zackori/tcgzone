<aside class="sidebar">

    <div class="logo">

        <h2>TCGZone</h2>

        <span>Admin</span>

    </div>

    <ul class="menu">

        <li class="<?= ($currentPage=="overview") ? "active" : "" ?>">

            <a href="overview.php">

                <i class="fa-solid fa-house"></i>

                <span>Overview</span>

            </a>

        </li>

        <li class="<?= ($currentPage=="orders") ? "active" : "" ?>">

            <a href="orders.php">

                <i class="fa-solid fa-cart-shopping"></i>

                <span>Orders</span>

            </a>

        </li>

        <li class="<?= ($currentPage=="financial") ? "active" : "" ?>">

            <a href="financial.php">

                <i class="fa-solid fa-coins"></i>

                <span>Financial</span>

            </a>

        </li>

        <li class="<?= ($currentPage=="inventory") ? "active" : "" ?>">

            <a href="inventory.php">

                <i class="fa-solid fa-box"></i>

                <span>Inventory</span>

            </a>

        </li>

    </ul>

</aside>