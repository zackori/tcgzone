<?php

$pageTitle = "Overview Dashboard";
$currentPage = "overview";

session_start();


?>
<!DOCTYPE html>

<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Admin | <?= htmlspecialchars($pageTitle) ?></title>


    <link rel="stylesheet" href="admin-shared.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <link rel="icon" type="image/svg" href="/tcgzone/assets/logos/logo/transparent-image.png">

</head>

<body>

    <div class="container">

        <?php include 'includes/sidebar.php'; ?>

        <main class="main">

            <?php include 'includes/header.php'; ?>

            <section class="cards">

                <div class="card-revenue card-clickable" id="revenueCard" role="button" tabindex="0">

                    <div class="card-info">

                        <p>Total Revenue</p>

                        <h2 id="salesCount">₱0</h2>

                    </div>

                    <div class="card-icon-revenue">

                        <i class="fa-solid fa-chart-line"></i>

                    </div>

                </div>

                <div class="card-success card-clickable" id="completedOrdersCard" role="button" tabindex="0">

                    <div class="card-info">

                        <p>Completed Orders</p>

                        <h2 id="ordersCount">0</h2>

                    </div>

                    <div class="card-icon-success">

                        <i class="fa-solid fa-box"></i>

                    </div>

                </div>



                <div class="card-cancel card-clickable" id="lowStockCard" role="button" tabindex="0">

                    <div class="card-info">

                        <p>Low Stock</p>

                        <h2 id="stockCount">0</h2>

                    </div>

                    <div class="card-icon-cancel">

                        <i class="fa-solid fa-triangle-exclamation"></i>

                    </div>

                </div>

                <div class="card-pending card-clickable" id="buyRequestCard" role="button" tabindex="0">

                    <div class="card-info">

                        <p>Pending Buy Request</p>

                        <h2 id="buyRequestCount">0</h2>

                    </div>

                    <div class="card-icon-pending">

                        <i class="fa-solid fa-credit-card"></i>

                    </div>

                </div>

            </section>


            <section class="chart-card">

                <div class="chart-title">Total Revenue</div>

                <canvas id="salesChart"></canvas>

            </section>

            <section class="chart-card">

                <div class="chart-title">Orders</div>

                <canvas id="ordersChart"></canvas>

            </section>



            <section class="chart-card">

                <div class="chart-title">Stocks</div>

                <canvas id="stockChart"></canvas>

            </section>
        </main>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="admin-shared.js?v=3"></script>

</body>

</html>
