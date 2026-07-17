<?php

$pageTitle="Overview Dashboard";
$currentPage="overview";

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

<section class="cards">

    <div class="card">

        <div class="card-info">

            <p>Total Orders</p>

            <h2 id="ordersCount">0</h2>

        </div>

        <div class="card-icon">

            <i class="fa-solid fa-box"></i>

        </div>

    </div>

    <div class="card">

        <div class="card-info">

            <p>Total Sales</p>

            <h2 id="salesCount">₱0</h2>

        </div>

        <div class="card-icon">

            <i class="fa-solid fa-chart-line"></i>

        </div>

    </div>

    <div class="card">

        <div class="card-info">

            <p>Out of Stock</p>

            <h2 id="stockCount">0</h2>

        </div>

        <div class="card-icon">

            <i class="fa-solid fa-cart-shopping"></i>

        </div>

    </div>

</section>

<section class="chart-card">

    <div class="chart-title">Orders</div>

    <canvas id="ordersChart"></canvas>

</section>

<section class="chart-card">

    <div class="chart-title">Total Sales</div>

    <canvas id="salesChart"></canvas>

</section>

<section class="chart-card">

    <div class="chart-title">Out of Stock</div>

    <canvas id="stockChart"></canvas>

</section>
    </main>

</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="admin-shared.js"></script>

</body>
</html>