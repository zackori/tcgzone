<?php
$pageTitle = "Sell Requests";
$currentPage = "sellRequest";

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

            <section class="users-cards">
                <div class="card-success card-clickable" id="approvedRequestsCard" role="button" tabindex="0" aria-pressed="false">
                    <div class="card-info">
                        <p>Approved Sell Requests</p>
                        <h2 id="approvedRequests">0</h2>
                    </div>
                    <div class="card-icon-success"><i class="fa-solid fa-square-check"></i></div>
                </div>

                <div class="card-pending card-clickable" id="pendingRequestsCard" role="button" tabindex="0" aria-pressed="false">
                    <div class="card-info">
                        <p>Pending Sell Requests</p>
                        <h2 id="pendingRequests">0</h2>
                    </div>
                    <div class="card-icon-pending"><i class="fa-solid fa-user-clock"></i></div>
                </div>
            </section>

            <div class="orders-card">
                <div class="orders-header">
                    <h3>Sell Requests</h3>
                    <div class="orders-actions">
                        <input type="text" id="searchSellRequest" placeholder="Search sell requests">
                        <select id="sellRequestStatusFilter" aria-label="Filter sell requests by status">
                            <option value="all">All Status</option>
                            <option value="Pending">Pending</option>
                            <option value="Approved">Approved</option>
                            <option value="Rejected">Rejected</option>
                        </select>
                    </div>
                </div>

                <table class="orders-table">
                    <thead>
                        <tr>
                            <th>Request ID</th>
                            <th>Username</th>
                            <th>Card Name</th>
                            <th>Quantity</th>
                            <th>Selling Price</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody id="sellRequestsTable"></tbody>
                </table>
            </div>

            <div class="admin-modal-overlay d-none" id="sellRequestDetailsModal" role="dialog" aria-modal="true" aria-labelledby="sellRequestDetailsTitle">
                <div class="admin-modal admin-modal-xl">
                    <div class="admin-modal-header">
                        <h3 id="sellRequestDetailsTitle">Sell Request Details</h3>
                        <button type="button" class="admin-modal-close" id="closeSellRequestDetailsModal" aria-label="Close">&times;</button>
                    </div>
                    <div class="admin-modal-body">
                        <div class="sell-request-details">
                            <img id="sellRequestImage" class="sell-request-image" alt="Requested card image">
                            <div class="order-details-meta">
                                <div class="modal-detail-row"><span>Username</span><strong id="sellRequestUsername">—</strong></div>
                                <div class="modal-detail-row"><span>Product ID</span><strong id="sellRequestProductId">—</strong></div>
                                <div class="modal-detail-row"><span>Card Name</span><strong id="sellRequestCardName">—</strong></div>
                                <div class="modal-detail-row"><span>Set Name</span><strong id="sellRequestSetName">—</strong></div>
                                <div class="modal-detail-row"><span>Category</span><strong id="sellRequestCategory">—</strong></div>
                                <div class="modal-detail-row"><span>Product Type</span><strong id="sellRequestProductType">—</strong></div>
                                <div class="modal-detail-row"><span>Rarity</span><strong id="sellRequestRarity">—</strong></div>
                                <div class="modal-detail-row"><span>Condition</span><strong id="sellRequestCondition">—</strong></div>
                                <div class="modal-detail-row"><span>Quantity</span><strong id="sellRequestQuantity">—</strong></div>
                                <div class="modal-detail-row"><span>Selling Price</span><strong id="sellRequestPrice">—</strong></div>
                                <div class="modal-detail-row"><span>Status</span><strong id="sellRequestStatus">—</strong></div>
                                <div class="modal-detail-row"><span>Notes</span><strong id="sellRequestNotes">—</strong></div>
                            </div>
                        </div>
                        <p class="modal-msg d-none" id="sellRequestDetailsMsg"></p>
                        <div class="modal-actions d-none" id="sellRequestReviewActions">
                            <button type="button" class="modal-btn reject" id="rejectSellRequest">Reject</button>
                            <button type="button" class="modal-btn accept" id="approveSellRequest">Approve</button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="admin-modal-overlay d-none" id="approveSellRequestModal" role="dialog" aria-modal="true" aria-labelledby="approveSellRequestTitle">
                <div class="admin-modal">
                    <div class="admin-modal-header">
                        <h3 id="approveSellRequestTitle">Approve Sell Request</h3>
                        <button type="button" class="admin-modal-close" id="closeApproveSellRequestModal" aria-label="Close">&times;</button>
                    </div>
                    <form class="admin-modal-body" id="approveSellRequestForm">
                        <p class="approval-product-summary" id="approvalProductSummary"></p>
                        <label class="sell-request-form-label" for="productCost">Product Cost</label>
                        <input class="sell-request-price-input" id="productCost" name="product_cost" type="number" min="0" step="0.01" required>
                        <label class="sell-request-form-label" for="approvedSellingPrice">Selling Price</label>
                        <input class="sell-request-price-input" id="approvedSellingPrice" name="selling_price" type="number" min="0" step="0.01" required>
                        <label class="sell-request-form-label" for="marketPrice">Market Price</label>
                        <input class="sell-request-price-input" id="marketPrice" name="market_price" type="number" min="0" step="0.01" required>
                        <p class="modal-msg d-none" id="approveSellRequestMsg"></p>
                        <div class="modal-actions">
                            <button type="button" class="modal-btn reject" id="cancelApproveSellRequest">Cancel</button>
                            <button type="submit" class="modal-btn accept" id="submitApprovedSellRequest">Add Product &amp; Approve</button>
                        </div>
                    </form>
                </div>
            </div>
        </main>
    </div>
    <script src="sell-request.js"></script>
</body>
</html>
