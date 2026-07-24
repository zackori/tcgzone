<?php

header("Content-Type: application/json");

include "../../../config/db_connect.php";

$orderId = isset($_GET["order_id"]) ? (int) $_GET["order_id"] : 0;

if ($orderId <= 0) {
    echo json_encode([
        "success" => false,
        "message" => "Invalid order ID."
    ]);
    exit;
}

$orderStmt = mysqli_prepare(
    $conn,
    "SELECT
        o.order_id,
        o.order_status AS status,
        o.order_date,
        o.total_amount,
        o.payment_method,
        CONCAT(u.first_name, ' ', u.last_name) AS customer_name
     FROM orders o
     LEFT JOIN users u ON o.user_id = u.id
     WHERE o.order_id = ?"
);
mysqli_stmt_bind_param($orderStmt, "i", $orderId);
mysqli_stmt_execute($orderStmt);
$orderResult = mysqli_stmt_get_result($orderStmt);
$order = mysqli_fetch_assoc($orderResult);
mysqli_stmt_close($orderStmt);

if (!$order) {
    echo json_encode([
        "success" => false,
        "message" => "Order not found."
    ]);
    exit;
}

$itemsStmt = mysqli_prepare(
    $conn,
    "SELECT p.card_name AS name, p.image, oi.quantity, oi.subtotal
     FROM order_items oi
     JOIN products p ON oi.product_id = p.product_id
     WHERE oi.order_id = ?"
);
mysqli_stmt_bind_param($itemsStmt, "i", $orderId);
mysqli_stmt_execute($itemsStmt);
$itemsResult = mysqli_stmt_get_result($itemsStmt);

$items = [];
$subtotalAmount = 0.0;

while ($row = mysqli_fetch_assoc($itemsResult)) {
    $itemSubtotal = (float) $row["subtotal"];
    $subtotalAmount += $itemSubtotal;

    $items[] = [
        "name" => $row["name"],
        "image" => $row["image"],
        "quantity" => (int) $row["quantity"],
        "subtotal" => $itemSubtotal
    ];
}

$shippingFee = max(0.0, (float) $order["total_amount"] - $subtotalAmount);

mysqli_stmt_close($itemsStmt);
mysqli_close($conn);

echo json_encode([
    "success" => true,
    "order" => [
        "order_id" => (int) $order["order_id"],
        "customer_name" => $order["customer_name"],
        "status" => $order["status"],
        "payment_method" => $order["payment_method"] ?? "cod",
        "order_date" => $order["order_date"],
        "subtotal_amount" => $subtotalAmount,
        "shipping_fee" => $shippingFee,
        "total_amount" => (float) $order["total_amount"],
        "items" => $items
    ]
]);
