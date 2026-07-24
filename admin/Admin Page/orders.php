<?php
$pageTitle = "Orders";
$currentPage = "orders";

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

            <!-- Orders content goes here -->

            <section class="orders-cards">

                <div class="card-neutral card-clickable" id="totalOrdersCard" role="button" tabindex="0">
                    <div class="card-info">
                        <p>Total Orders</p>
                        <h2 id="totalOrders">0</h2>
                    </div>

                    <div class="card-icon-neutral">
                        <i class="fa-solid fa-cart-shopping"></i>
                    </div>
                </div>

                <div class="card-pending card-clickable" id="pendingOrdersCard" role="button" tabindex="0">
                    <div class="card-info">
                        <p>On-going Orders</p>
                        <h2 id="ongoingOrders">0</h2>
                    </div>

                    <div class="card-icon-pending">
                        <i class="fa-solid fa-truck"></i>
                    </div>
                </div>

                <div class="card-success card-clickable" id="completedOrdersCard" role="button" tabindex="0">
                    <div class="card-info">
                        <p>Completed Orders</p>
                        <h2 id="completedOrders">0</h2>
                    </div>

                    <div class="card-icon-success">
                        <i class="fa-solid fa-circle-check"></i>
                    </div>
                </div>

                <div class="card-cancel card-clickable" id="cancelRequestCard" role="button" tabindex="0">
                    <div class="card-info">
                        <p>Cancel Request</p>
                        <h2 id="cancelRequestCount">0</h2>
                    </div>

                    <div class="card-icon-cancel">
                        <i class="fa-solid fa-circle-xmark"></i>
                    </div>
                </div>

            </section>

            <div class="admin-modal-overlay d-none" id="cancelRequestsListModal">
                <div class="admin-modal admin-modal-wide">
                    <div class="admin-modal-header">
                        <h3>Cancel Requests</h3>
                        <button type="button" class="admin-modal-close"
                            id="closeCancelRequestsListModal">&times;</button>
                    </div>

                    <div class="admin-modal-body">
                        <div class="cancel-requests-grid" id="cancelRequestsGrid"></div>
                        <p class="modal-empty-msg d-none" id="cancelRequestsEmptyMsg">No cancel requests yet.</p>
                    </div>
                </div>
            </div>

            <div class="admin-modal-overlay d-none" id="cancelRequestModal">
                <div class="admin-modal">
                    <div class="admin-modal-header">
                        <h3>Cancel Request</h3>
                        <button type="button" class="admin-modal-close" id="closeCancelRequestModal">&times;</button>
                    </div>

                    <div class="admin-modal-body">
                        <div class="modal-detail-row">
                            <span>Order ID</span>
                            <strong id="modalOrderId">—</strong>
                        </div>
                        <div class="modal-detail-row">
                            <span>Reason</span>
                            <strong id="modalReason">—</strong>
                        </div>
                        <div class="modal-detail-row">
                            <span>Requested At</span>
                            <strong id="modalRequestedAt">—</strong>
                        </div>
                        <div class="modal-detail-row">
                            <span>Status</span>
                            <strong id="modalStatus">—</strong>
                        </div>

                        <div class="modal-actions d-none" id="modalReviewActions">
                            <button type="button" class="modal-btn reject" id="rejectCancelRequest">Reject</button>
                            <button type="button" class="modal-btn accept" id="acceptCancelRequest">Accept</button>
                        </div>

                        <p class="modal-msg d-none" id="cancelRequestModalMsg"></p>
                    </div>
                </div>
            </div>

            <div class="admin-modal-overlay d-none" id="orderDetailsModal">
                <div class="admin-modal admin-modal-xl">
                    <div class="admin-modal-header">
                        <h3 id="orderDetailsTitle">Order Details</h3>
                        <button type="button" class="admin-modal-close" id="closeOrderDetailsModal">&times;</button>
                    </div>

                    <div class="admin-modal-body">
                        <div class="order-details-meta">
                            <div class="modal-detail-row">
                                <span>Customer</span>
                                <strong id="orderDetailsCustomer">—</strong>
                            </div>
                            <div class="modal-detail-row">
                                <span>Status</span>
                                <strong id="orderDetailsStatus">—</strong>
                            </div>
                            <div class="modal-detail-row">
                                <span>Payment</span>
                                <strong id="orderDetailsPayment">—</strong>
                            </div>
                            <div class="modal-detail-row">
                                <span>Date</span>
                                <strong id="orderDetailsDate">—</strong>
                            </div>
                        </div>

                        <div class="order-items-panel">
                            <table class="order-items-table">
                                <thead>
                                    <tr>
                                        <th>Product</th>
                                        <th>Quantity</th>
                                        <th>Subtotal</th>
                                    </tr>
                                </thead>
                                <tbody id="orderDetailsItems"></tbody>
                            </table>
                        </div>

                        <div class="order-details-summary">
                            <div class="order-summary-row">
                                <span>Subtotal</span>
                                <strong id="orderDetailsSubtotal">₱0</strong>
                            </div>
                            <div class="order-summary-row">
                                <span>Shipping Fee</span>
                                <strong id="orderDetailsShipping">₱0</strong>
                            </div>
                            <div class="order-summary-row">
                                <span>Total</span>
                                <strong id="orderDetailsTotal">₱0</strong>
                            </div>
                        </div>

                        <div class="procurement-status-update">
                            <label for="orderDetailsStatusSelect">Update status</label>
                            <select id="orderDetailsStatusSelect">
                                <option value="Pending">Pending</option>
                                <option value="Processing">Processing</option>
                                <option value="In Transit">In Transit</option>
                                <option value="Delivered">Delivered</option>
                                <option value="Cancelled">Cancelled</option>
                            </select>
                            <button type="button" class="modal-btn accept" id="updateOrderDetailsStatus">Update</button>
                        </div>

                        <p class="modal-msg d-none" id="orderDetailsMsg"></p>
                    </div>
                </div>
            </div>

            <div class="orders-card">

                <div class="orders-header">

                    <h3>All Orders</h3>

                    <div class="orders-actions">

                        <input type="text" id="searchOrder" placeholder="Search customer or order">

                        <select id="statusFilter">

                            <option value="all">All Status</option>
                            <option value="ongoing">Ongoing</option>
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
                            <th>Shipping Address</th>
                            <th>Total</th>
                            <th>Payment</th>
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

    <script src="orders.js?v=2"></script>

</body>

</html>
