<?php

include("../../../config/db_connect.php");

$response = [];

/* Total Orders */

$sql = "SELECT COUNT(*) totalOrders FROM orders WHERE order_status = 'Delivered'";

$result = $conn->query($sql);

$row = $result->fetch_assoc();

$response['orders'] = $row['totalOrders'];

/* Total Sales */

$sql = "SELECT SUM(total_amount) totalSales FROM orders WHERE order_status = 'Delivered'";

$result = $conn->query($sql);

$row = $result->fetch_assoc();

$response['sales'] = $row['totalSales'] ?? 0;

/* Out of Stock */

$sql = "SELECT COUNT(*) outStock FROM products WHERE stock_quantity <= 3";

$result = $conn->query($sql);

$row = $result->fetch_assoc();

$response['stock_quantity'] = $row['outStock'];

echo json_encode($response);

?>