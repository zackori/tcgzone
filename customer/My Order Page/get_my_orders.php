<?php
require '../../config/db_connect.php';
session_start();

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'You must be signed in to view your orders.']);
    exit;
}

$userId = $_SESSION['user_id'];

// Every order this user has placed, newest first
$ordersStmt = $conn->prepare(
    "SELECT order_id, order_status, order_date, total_amount
     FROM orders
     WHERE user_id = ?
     ORDER BY order_date DESC"
);
$ordersStmt->bind_param("i", $userId);
$ordersStmt->execute();
$orderRows = $ordersStmt->get_result()->fetch_all(MYSQLI_ASSOC);

$orders = [];

foreach ($orderRows as $orderRow) {
    $orderId = (int) $orderRow['order_id'];

    // Line items for this specific order, joined to products for the
    // name/image — same join shape as get-order.php uses on the admin
    // side, just scoped per-order here instead of per-status-table.
    $itemsStmt = $conn->prepare(
        "SELECT p.card_name AS name, p.image, oi.quantity, oi.subtotal
         FROM order_items oi
         JOIN products p ON oi.product_id = p.product_id
         WHERE oi.order_id = ?"
    );
    $itemsStmt->bind_param("i", $orderId);
    $itemsStmt->execute();
    $itemRows = $itemsStmt->get_result()->fetch_all(MYSQLI_ASSOC);

    $items = array_map(function ($item) {
        return [
            'name' => $item['name'],
            'image' => $item['image'],
            'quantity' => (int) $item['quantity'],
            'subtotal' => (float) $item['subtotal']
        ];
    }, $itemRows);

    $subtotalAmount = array_reduce($items, function ($carry, $item) {
        return $carry + $item['subtotal'];
    }, 0.0);

    $shippingFee = max(0.0, (float) $orderRow['total_amount'] - $subtotalAmount);

    $cancelRequest = null;
    $cancelStmt = $conn->prepare(
        "SELECT status FROM cancel_requests WHERE order_id = ?"
    );
    $cancelStmt->bind_param('i', $orderId);
    $cancelStmt->execute();
    $cancelRow = $cancelStmt->get_result()->fetch_assoc();
    $cancelStmt->close();

    if ($cancelRow) {
        $cancelRequest = [
            'status' => $cancelRow['status']
        ];
    }

    $orders[] = [
        'orderId' => $orderId,
        'status' => $orderRow['order_status'],
        'orderDate' => $orderRow['order_date'],
        'subtotal' => $subtotalAmount,
        'shippingFee' => $shippingFee,
        'total' => (float) $orderRow['total_amount'],
        'items' => $items,
        'cancelRequest' => $cancelRequest
    ];
}

echo json_encode(['success' => true, 'orders' => $orders]);

mysqli_close($conn);
