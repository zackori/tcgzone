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
<link rel="stylesheet" href="financial.css?v=5">

<link rel="stylesheet"
href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
<link rel="icon" type="image/svg" href="/tcgzone/assets/logos/logo/transparent-image.png">

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

<div class="card-revenue card-clickable" id="revenueDetailsCard" role="button" tabindex="0">
        <div class="card-info">
            <p>Total Revenue</p>
            <h2 id="totalRevenue">₱0</h2>
        </div>

        <div class="card-icon-revenue">

            <i class="fa-solid fa-chart-line"></i>

        </div>
</div>


<div class="card-gross-profit card-clickable" id="grossProfitDetailsCard" role="button" tabindex="0">
        <div class="card-info">
            <p>Gross Profit</p>
            <h2 id="netProfit">₱0</h2>
        </div>

        <div class="card-icon-gross-profit">
            <i class="fa-solid fa-money-bill-trend-up"></i>
        </div>
</div>

<div class="admin-modal-overlay d-none" id="grossProfitDetailsModal" role="dialog" aria-modal="true" aria-labelledby="grossProfitDetailsTitle">
    <div class="admin-modal finance-modal">
        <div class="admin-modal-header">
            <h3 id="grossProfitDetailsTitle">Gross Profit</h3>
            <button type="button" class="admin-modal-close" id="closeGrossProfitDetailsModal" aria-label="Close">&times;</button>
        </div>
        <div class="admin-modal-body">
            <div class="finance-panel" id="grossProfitSummary"></div>
            <div class="finance-formula" id="grossProfitFormula"></div>
        </div>
    </div>
</div>

<div class="card-receipt card-clickable" id="averageOrderDetailsCard" role="button" tabindex="0">
        <div class="card-info">
            <p>Average Order</p>
            <h2 id="averageOrder">₱0</h2>
        </div>

        <div class="card-icon-receipt">
            <i class="fa-solid fa-receipt"></i>
        </div>
</div>

<div class="admin-modal-overlay d-none" id="averageOrderDetailsModal" role="dialog" aria-modal="true" aria-labelledby="averageOrderDetailsTitle">
    <div class="admin-modal finance-modal">
        <div class="admin-modal-header">
            <h3 id="averageOrderDetailsTitle">Average Order</h3>
            <button type="button" class="admin-modal-close" id="closeAverageOrderDetailsModal" aria-label="Close">&times;</button>
        </div>
        <div class="admin-modal-body">
            <div class="finance-panel" id="averageOrderSummary"></div>
            <div class="finance-formula" id="averageOrderFormula"></div>
        </div>
    </div>
</div>

<div class="card-success card-clickable" id="financialCompletedOrdersCard" role="button" tabindex="0">
        <div class="card-info">
            <p>Completed Orders</p>
            <h2 id="completedOrders">0</h2>
        </div>

        <div class="card-icon-success">
            <i class="fa-solid fa-circle-check"></i>
        </div>
</div>




</section>

<div class="admin-modal-overlay d-none" id="revenueDetailsModal" role="dialog" aria-modal="true" aria-labelledby="revenueDetailsTitle">
    <div class="admin-modal finance-modal">
        <div class="admin-modal-header">
            <h3 id="revenueDetailsTitle">Total Revenue</h3>
            <button type="button" class="admin-modal-close" id="closeRevenueDetailsModal" aria-label="Close">&times;</button>
        </div>
        <div class="admin-modal-body">
            <div class="finance-panel" id="revenueDetailsSummary"></div>
            <button type="button" class="monthly-revenue-toggle" id="monthlyRevenueToggle" aria-expanded="false">
                <span>Monthly Revenue</span><i class="fa-solid fa-chevron-down"></i>
            </button>
            <div class="monthly-revenue-content d-none" id="monthlyRevenueContent">
                <label class="revenue-year-label">Year <select id="revenueYearSelect"></select></label>
                <div class="finance-table-wrap">
                    <table class="finance-table"><thead><tr><th>Month</th><th>Orders</th><th>Revenue</th></tr></thead><tbody id="monthlyRevenueRows"></tbody></table>
                </div>
            </div>
        </div>
    </div>
</div>

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

<h3>Gross Profit</h3>

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

<script src="financial.js?v=11"></script>

</body>
</html>
