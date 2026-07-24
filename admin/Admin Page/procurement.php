<?php
$pageTitle = "Procurement";
$currentPage = "procurement";
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

            <section class="procurement-cards">
                <div class="card-cancel card-clickable" id="lowStockCard" role="button" tabindex="0">
                    <div class="card-info"><p>Low Stock</p><h2 id="stockCount">0</h2></div>
                    <div class="card-icon-cancel"><i class="fa-solid fa-triangle-exclamation"></i></div>
                </div>
                <div class="card-success card-clickable" id="successBuyCard" role="button" tabindex="0" aria-pressed="false">
                    <div class="card-info">
                        <p>Successful Buy Requests</p>
                        <h2 id="successBuyCount">0</h2>
                    </div>
                    <div class="card-icon-success"><i class="fa-solid fa-square-check"></i></div>
                </div>
                <div class="card-pending card-clickable" id="buyRequestCard" role="button" tabindex="0">
                    <div class="card-info"><p>Pending Buy Request</p><h2 id="buyRequestCount">0</h2></div>
                    <div class="card-icon-pending"><i class="fa-solid fa-credit-card"></i></div>
                </div>
            </section>

            <section class="orders-card">
                <div class="orders-header">
                    <h3>Procurement Receipts</h3>
                    <div class="orders-actions">
                        <button type="button" class="modal-btn accept" id="addPurchasedCards">Add Purchased Cards</button>
                        <input type="text" id="searchProcurement" placeholder="Search supplier or receipt">
                        <select id="procurementStatusFilter">
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
                    <thead><tr><th>Receipt ID</th><th>Supplier</th><th>Total</th><th>Status</th><th>Date</th></tr></thead>
                    <tbody id="procurementTable"></tbody>
                </table>
            </section>

            <div class="admin-modal-overlay d-none" id="purchasedCardsModal" role="dialog" aria-modal="true" aria-labelledby="purchasedCardsTitle">
                <div class="admin-modal admin-modal-xl purchased-cards-modal">
                    <div class="admin-modal-header"><h3 id="purchasedCardsTitle">Add Purchased Cards</h3><button type="button" class="admin-modal-close" id="closePurchasedCardsModal">&times;</button></div>
                    <form class="admin-modal-body" id="purchasedCardsForm">
                        <div id="purchasedCardsList"></div>
                        <p class="modal-msg d-none" id="purchasedCardsMessage"></p>
                        <div class="modal-actions purchased-cards-actions">
                            <button type="button" class="modal-btn reject" id="addAnotherPurchasedCard"><i class="fa-solid fa-plus"></i> Add Another Card</button>
                            <button type="submit" class="modal-btn accept" id="insertPurchasedCards">Insert Card(s)</button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="admin-modal-overlay d-none" id="lowStockModal" role="dialog" aria-modal="true" aria-labelledby="lowStockTitle">
                <div class="admin-modal admin-modal-xl procurement-low-stock-modal">
                    <div class="admin-modal-header"><h3 id="lowStockTitle">Low Stock Resupply</h3><button type="button" class="admin-modal-close" id="closeLowStockModal">&times;</button></div>
                    <div class="admin-modal-body">
                        <div class="procurement-supplier-row">
                            <label for="supplierSelect">Trusted supplier</label>
                            <select id="supplierSelect"></select>
                            <button type="button" class="modal-btn accept" id="placeProcurementOrder">Place Order</button>
                        </div>
                        <p class="modal-msg d-none" id="lowStockMessage"></p>
                        <div class="procurement-table-wrap">
                            <table class="order-items-table procurement-items-table">
                                <colgroup>
                                    <col class="resupply-col"><col class="card-col"><col class="set-col"><col class="category-col"><col class="type-col">
                                    <col class="rarity-col"><col class="condition-col"><col class="stock-col"><col class="cost-col"><col class="quantity-col">
                                </colgroup>
                                <thead><tr><th>Resupply</th><th>Card</th><th>Set</th><th>Category</th><th>Type</th><th>Rarity</th><th>Condition</th><th>In Stock</th><th>Market Cost</th><th>Order Qty.</th></tr></thead>
                                <tbody id="lowStockTable"></tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div class="admin-modal-overlay d-none" id="procurementDetailsModal" role="dialog" aria-modal="true" aria-labelledby="procurementDetailsTitle">
                <div class="admin-modal admin-modal-xl">
                    <div class="admin-modal-header"><h3 id="procurementDetailsTitle">Procurement Receipt</h3><button type="button" class="admin-modal-close" id="closeProcurementDetailsModal">&times;</button></div>
                    <div class="admin-modal-body">
                        <div class="order-details-meta">
                            <div class="modal-detail-row"><span>Supplier</span><strong id="procurementSupplier">—</strong></div>
                            <div class="modal-detail-row"><span>Status</span><strong id="procurementStatus">—</strong></div>
                            <div class="modal-detail-row"><span>Date</span><strong id="procurementDate">—</strong></div>
                        </div>
                        <div class="order-items-panel"><table class="order-items-table"><thead><tr><th>Card</th><th>Quantity</th><th>Market Cost</th><th>Subtotal</th></tr></thead><tbody id="procurementDetailsItems"></tbody></table></div>
                        <div class="order-details-summary"><div class="order-summary-row"><span>Total</span><strong id="procurementTotal">₱0.00</strong></div></div>
                        <div class="procurement-status-update"><label for="procurementStatusSelect">Update status</label><select id="procurementStatusSelect"><option>Pending</option><option>Processing</option><option>In Transit</option><option>Delivered</option><option>Cancelled</option></select><button type="button" class="modal-btn accept" id="updateProcurementStatus">Update</button></div>
                        <p class="modal-msg d-none" id="procurementDetailsMessage"></p>
                    </div>
                </div>
            </div>
        </main>
    </div>
    <script src="procurement.js?v=7"></script>
</body>
</html>
