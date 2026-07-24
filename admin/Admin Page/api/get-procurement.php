<?php
header("Content-Type: application/json; charset=utf-8");
require_once __DIR__ . "/../../../config/db_connect.php";

$result = mysqli_query($conn, "SELECT po.procurement_order_id, po.total_amount, po.order_status, po.order_date, s.supplier_name FROM procurement_orders po JOIN suppliers s ON s.supplier_id = po.supplier_id ORDER BY po.order_date DESC, po.procurement_order_id DESC");
$orders = [];
while ($row = mysqli_fetch_assoc($result)) $orders[] = $row;
mysqli_close($conn);
echo json_encode($orders);
