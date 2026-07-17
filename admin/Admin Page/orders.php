<?php
$pageTitle = "Orders";
$currentPage = "orders";

session_start();

if (!isset($_SESSION['admin']) || $_SESSION['admin'] !== true) {
    header("Location: /tcgzone/customer/Login Page/login.html");
    exit;
}

?>

<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title><?= htmlspecialchars($pageTitle) ?></title>

<link rel="stylesheet" href="admin-shared.css">

<link rel="stylesheet"
href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

</head>

<body>

<div class="container">

    <?php include 'includes/sidebar.php'; ?>

    <main class="main">

        <?php include 'includes/header.php'; ?>

        <!-- Orders content goes here -->
         
        <section class="cards">

    <div class="card">
        <div class="card-info">
            <p>Total Orders</p>
            <h2 id="totalOrders">0</h2>
        </div>

        <div class="card-icon">
            <i class="fa-solid fa-cart-shopping"></i>
        </div>
    </div>

    <div class="card">
        <div class="card-info">
            <p>On-going Orders</p>
            <h2 id="ongoingOrders">0</h2>
        </div>

        <div class="card-icon">
            <i class="fa-solid fa-truck"></i>
        </div>
    </div>

    <div class="card">
        <div class="card-info">
            <p>Completed Orders</p>
            <h2 id="completedOrders">0</h2>
        </div>

        <div class="card-icon">
            <i class="fa-solid fa-circle-check"></i>
        </div>
    </div>

</section>

<div class="orders-card">

    <div class="orders-header">

        <h3>All Orders</h3>

        <div class="orders-actions">

            <input
                type="text"
                id="searchOrder"
                placeholder="Search customer or order">

            <select id="statusFilter">

    <option value="all">All Status</option>
    <option value="Pending">Pending</option>
    <option value="Processing">Processing</option>
    <option value="In Transit">In Transit</option>
    <option value="Delivered">Delivered</option>
    <option value="Cancelled">Cancelled</option>

</select>

        </div>

    </div>

    <table class="orders-table">

        <thead>

            <tr>

                <th>Order ID</th>
                <th>Customer</th>
                <th>Pokemon</th>
                <th>Quantity</th>
                <th>Total</th>
                <th>Status</th>
                <th>Date</th>

            </tr>

        </thead>

        <tbody id="ordersTable">

        </tbody>

    </table>

    <div class="pagination">

        <button>
            <i class="fa-solid fa-angle-left"></i>
        </button>

        <button class="active">1</button>

        <button>
            <i class="fa-solid fa-angle-right"></i>
        </button>

    </div>

</div>


    </main>

</div>

<script src="orders.js"></script>

</body>
</html>