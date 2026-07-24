<?php
header("Content-Type: application/json; charset=utf-8");
require_once __DIR__ . "/../../../config/db_connect.php";

$data = json_decode(file_get_contents("php://input"), true) ?: [];
$supplierId = isset($data["supplier_id"]) ? (int) $data["supplier_id"] : 0;
$items = $data["items"] ?? [];
if ($supplierId <= 0 || !is_array($items) || count($items) === 0) { http_response_code(422); echo json_encode(["success" => false, "message" => "Choose a supplier and at least one card to resupply."]); exit; }

mysqli_begin_transaction($conn);
try {
    $supplierStmt = mysqli_prepare($conn, "SELECT supplier_id FROM suppliers WHERE supplier_id = ?");
    mysqli_stmt_bind_param($supplierStmt, "i", $supplierId);
    mysqli_stmt_execute($supplierStmt);
    if (!mysqli_fetch_assoc(mysqli_stmt_get_result($supplierStmt))) throw new Exception("Selected supplier was not found.");
    mysqli_stmt_close($supplierStmt);

    $requested = [];
    foreach ($items as $item) {
        $productId = isset($item["product_id"]) ? (int) $item["product_id"] : 0;
        $quantity = isset($item["quantity"]) ? (int) $item["quantity"] : 0;
        if ($productId > 0 && $quantity > 0) $requested[$productId] = $quantity;
    }
    if (!$requested) throw new Exception("Choose at least one valid resupply quantity.");

    $productStmt = mysqli_prepare($conn, "SELECT product_id, market_price FROM products WHERE product_id = ? AND stock_quantity <= 3 FOR UPDATE");
    $lineItems = [];
    $total = 0.0;
    foreach ($requested as $productId => $quantity) {
        mysqli_stmt_bind_param($productStmt, "i", $productId);
        mysqli_stmt_execute($productStmt);
        $product = mysqli_fetch_assoc(mysqli_stmt_get_result($productStmt));
        if (!$product) throw new Exception("A selected product is no longer low in stock.");
        $unitCost = (float) ($product["market_price"] ?? 0);
        $subtotal = $unitCost * $quantity;
        $lineItems[] = [$productId, $quantity, $unitCost, $subtotal];
        $total += $subtotal;
    }
    mysqli_stmt_close($productStmt);

    $orderStmt = mysqli_prepare($conn, "INSERT INTO procurement_orders (supplier_id, total_amount) VALUES (?, ?)");
    mysqli_stmt_bind_param($orderStmt, "id", $supplierId, $total);
    mysqli_stmt_execute($orderStmt);
    $orderId = mysqli_insert_id($conn);
    mysqli_stmt_close($orderStmt);

    $itemStmt = mysqli_prepare($conn, "INSERT INTO procurement_order_items (procurement_order_id, product_id, quantity, unit_cost, subtotal) VALUES (?, ?, ?, ?, ?)");
    foreach ($lineItems as [$productId, $quantity, $unitCost, $subtotal]) {
        mysqli_stmt_bind_param($itemStmt, "iiidd", $orderId, $productId, $quantity, $unitCost, $subtotal);
        mysqli_stmt_execute($itemStmt);
    }
    mysqli_stmt_close($itemStmt);
    mysqli_commit($conn);
    echo json_encode(["success" => true, "procurement_order_id" => $orderId]);
} catch (Throwable $error) {
    mysqli_rollback($conn);
    http_response_code(422);
    echo json_encode(["success" => false, "message" => $error->getMessage()]);
}
mysqli_close($conn);
