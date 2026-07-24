<?php
header("Content-Type: application/json; charset=utf-8");
require_once __DIR__ . "/../../../config/db_connect.php";

$orderId = isset($_GET["procurement_order_id"]) ? (int) $_GET["procurement_order_id"] : 0;
if ($orderId <= 0) { echo json_encode(["success" => false, "message" => "Invalid receipt ID."]); exit; }

$orderStmt = mysqli_prepare($conn, "SELECT po.procurement_order_id, po.total_amount, po.order_status, po.order_date, s.supplier_name FROM procurement_orders po JOIN suppliers s ON s.supplier_id = po.supplier_id WHERE po.procurement_order_id = ?");
mysqli_stmt_bind_param($orderStmt, "i", $orderId);
mysqli_stmt_execute($orderStmt);
$order = mysqli_fetch_assoc(mysqli_stmt_get_result($orderStmt));
mysqli_stmt_close($orderStmt);
if (!$order) { echo json_encode(["success" => false, "message" => "Receipt not found."]); exit; }

$itemsStmt = mysqli_prepare($conn, "SELECT p.card_name, p.set_name, poi.quantity, poi.unit_cost, poi.subtotal FROM procurement_order_items poi JOIN products p ON p.product_id = poi.product_id WHERE poi.procurement_order_id = ?");
mysqli_stmt_bind_param($itemsStmt, "i", $orderId);
mysqli_stmt_execute($itemsStmt);
$itemsResult = mysqli_stmt_get_result($itemsStmt);
$items = [];
while ($row = mysqli_fetch_assoc($itemsResult)) $items[] = $row;
mysqli_stmt_close($itemsStmt);
mysqli_close($conn);
echo json_encode(["success" => true, "order" => $order, "items" => $items]);
