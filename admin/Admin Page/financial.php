<?php
$pageTitle = "Financial";
$currentPage = "financial";
?>

<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Admin | <?= htmlspecialchars($pageTitle) ?></title>

<link rel="stylesheet" href="admin-shared.css">
<link rel="stylesheet" href="financial.css">

<link rel="stylesheet"
href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
<link rel="icon" type="image/svg" href="/tcgzone/assets/logos/logo/transparent-image.png">

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

</head>

<body>

<div class="container">

<?php include "includes/sidebar.php"; ?>

<main class="main">

<?php include "includes/header.php"; ?>

<!-- ========================= -->
<!-- CARDS -->
<!-- ========================= -->

<section class="financial-cards">

<div class="card-revenue">
        <div class="card-info">
            <p>Total Revenue</p>
            <h2 id="totalRevenue">₱0</h2>
        </div>

        <div class="card-icon-revenue">

            <i class="fa-solid fa-chart-line"></i>

        </div>
</div>


<div class="card-gross-profit">
        <div class="card-info">
            <p>Gross Profit</p>
            <h2 id="netProfit">₱0</h2>
        </div>

        <div class="card-icon-gross-profit">
            <i class="fa-solid fa-money-bill-trend-up"></i>
        </div>
</div>

<div class="card-receipt">
        <div class="card-info">
            <p>Average Order</p>
            <h2 id="averageOrder">₱0</h2>
        </div>

        <div class="card-icon-receipt">
            <i class="fa-solid fa-receipt"></i>
        </div>
</div>

<div class="card-success">
        <div class="card-info">
            <p>Completed Orders</p>
            <h2 id="completedOrders">0</h2>
        </div>

        <div class="card-icon-success">
            <i class="fa-solid fa-circle-check"></i>
        </div>
</div>




</section>

<!-- ========================= -->
<!-- CHART 1 -->
<!-- ========================= -->

<div class="chart-card">

<h3>Total Revenue</h3>

<canvas id="revenueChart"></canvas>

</div>

<!-- ========================= -->
<!-- CHART 2 -->
<!-- ========================= -->

<div class="chart-card">

<h3>Net Profit</h3>

<canvas id="profitChart"></canvas>

</div>

<!-- ========================= -->
<!-- CHART 3 -->
<!-- ========================= -->

<div class="chart-card">

<h3>Monthly Orders</h3>

<canvas id="ordersChart"></canvas>

</div>

</main>

</div>

<script src="financial.js"></script>

</body>
</html>