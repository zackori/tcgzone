<?php
header("Content-Type: application/json; charset=utf-8");
require_once __DIR__ . "/../../../config/db_connect.php";

$data = json_decode(file_get_contents("php://input"), true) ?: [];
$orderId = isset($data["procurement_order_id"]) ? (int) $data["procurement_order_id"] : 0;
$status = trim($data["status"] ?? "");
$allowed = ["Pending", "Processing", "In Transit", "Delivered", "Cancelled"];
if ($orderId <= 0 || !in_array($status, $allowed, true)) { http_response_code(422); echo json_encode(["success" => false, "message" => "Invalid status update."]); exit; }

mysqli_begin_transaction($conn);
try {
    $orderStmt = mysqli_prepare($conn, "SELECT order_status FROM procurement_orders WHERE procurement_order_id = ? FOR UPDATE");
    mysqli_stmt_bind_param($orderStmt, "i", $orderId);
    mysqli_stmt_execute($orderStmt);
    $orderResult = mysqli_stmt_get_result($orderStmt);
    $order = mysqli_fetch_assoc($orderResult);
    mysqli_free_result($orderResult);
    mysqli_stmt_close($orderStmt);
    if (!$order) throw new Exception("Procurement receipt not found.");
    if (in_array($order["order_status"], ["Delivered", "Cancelled"], true) && $status !== $order["order_status"]) {
        throw new Exception("Delivered and cancelled receipts are final and cannot be changed.");
    }

    if ($order["order_status"] !== "Delivered" && $status === "Delivered") {
        $itemsStmt = mysqli_prepare($conn, "SELECT product_id, quantity FROM procurement_order_items WHERE procurement_order_id = ?");
        mysqli_stmt_bind_param($itemsStmt, "i", $orderId);
        mysqli_stmt_execute($itemsStmt);
        $itemsResult = mysqli_stmt_get_result($itemsStmt);
        $items = [];
        while ($item = mysqli_fetch_assoc($itemsResult)) {
            $items[] = [
                "quantity" => (int) $item["quantity"],
                "product_id" => (int) $item["product_id"]
            ];
        }
        mysqli_free_result($itemsResult);
        mysqli_stmt_close($itemsStmt);

        $stockStmt = mysqli_prepare($conn, "UPDATE products SET stock_quantity = stock_quantity + ? WHERE product_id = ?");
        foreach ($items as $item) {
            $quantity = $item["quantity"];
            $productId = $item["product_id"];
            mysqli_stmt_bind_param($stockStmt, "ii", $quantity, $productId);
            mysqli_stmt_execute($stockStmt);
        }
        mysqli_stmt_close($stockStmt);
    }
    $updateStmt = mysqli_prepare($conn, "UPDATE procurement_orders SET order_status = ? WHERE procurement_order_id = ?");
    mysqli_stmt_bind_param($updateStmt, "si", $status, $orderId);
    mysqli_stmt_execute($updateStmt); mysqli_stmt_close($updateStmt);
    mysqli_commit($conn);
    echo json_encode(["success" => true, "status" => $status]);
} catch (Throwable $error) {
    try {
        mysqli_rollback($conn);
    } catch (Throwable $rollbackError) {
        // Preserve the original database error while guaranteeing a JSON response.
    }
    http_response_code(422);
    echo json_encode(["success" => false, "message" => $error->getMessage()]);
}
mysqli_close($conn);
