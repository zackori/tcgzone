<?php
header("Content-Type: application/json; charset=utf-8");
require_once __DIR__ . "/../../../config/db_connect.php";

$result = mysqli_query($conn, "SELECT po.procurement_order_id, po.total_amount, po.order_status, po.order_date, s.supplier_name, COALESCE((SELECT SUM(poi.subtotal) FROM procurement_order_items poi WHERE poi.procurement_order_id = po.procurement_order_id), 0) AS items_subtotal FROM procurement_orders po JOIN suppliers s ON s.supplier_id = po.supplier_id ORDER BY po.order_date DESC, po.procurement_order_id DESC");
$orders = [];
while ($row = mysqli_fetch_assoc($result)) {
    $itemsSubtotal = (float) ($row["items_subtotal"] ?? 0);
    $shippingFee = strtolower(trim($row["supplier_name"] ?? "")) === "tcgzone" ? 0.0 : 200.0;
    $row["items_subtotal"] = $itemsSubtotal;
    $row["shipping_fee"] = $shippingFee;
    $row["display_total_amount"] = $itemsSubtotal + $shippingFee;
    $orders[] = $row;
}
mysqli_close($conn);
echo json_encode($orders);
