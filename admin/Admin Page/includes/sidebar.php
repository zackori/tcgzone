<aside class="sidebar">

    <div class="logo">

        <h2><a href="overview.php">TCGZone</a></h2>

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

                <i class="fa-solid fa-truck"></i>

                <span>Orders</span>

            </a>

        </li>

        <li class="<?= ($currentPage=="sellRequest") ? "active" : "" ?>">

            <a href="sell-request.php">

                <i class="fa-solid fa-hand-holding-dollar"></i>

                <span>Sell Request</span>

            </a>

        </li>

        <li class="<?= ($currentPage=="inventory") ? "active" : "" ?>">

            <a href="inventory.php">

                <i class="fa-solid fa-warehouse"></i>

                <span>Inventory</span>

            </a>

        </li>

        <li class="<?= ($currentPage=="procurement") ? "active" : "" ?>">

            <a href="procurement.php">

                <i class="fa-solid fa-handshake"></i>

                <span>Procurement</span>

            </a>

        </li>

        

        <li class="<?= ($currentPage=="financial") ? "active" : "" ?>">

            <a href="financial.php">

                <i class="fa-solid fa-coins"></i>

                <span>Financial</span>

            </a>

        </li>

    

        <li class="<?= ($currentPage=="reviews") ? "active" : "" ?>">

            <a href="reviews.php">

                <i class="fa-solid fa-star"></i>

                <span>Reviews</span>

            </a>

        </li>
        
        <li class="<?= ($currentPage=="users") ? "active" : "" ?>">

            <a href="users.php">

                <i class="fa-solid fa-user"></i>

                <span>Users</span>

            </a>

        </li>

    </ul>

</aside>