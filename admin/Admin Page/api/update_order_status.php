<?php

header("Content-Type: application/json");

require_once __DIR__ . "/../../../config/db_connect.php";

$data = json_decode(file_get_contents("php://input"), true);

$order_id = isset($data["order_id"]) ? (int) $data["order_id"] : 0;
$status = trim($data["status"] ?? "");
$allowedStatuses = ["Pending", "Processing", "In Transit", "Delivered", "Cancelled"];

if ($order_id <= 0 || !in_array($status, $allowedStatuses, true)) {
    http_response_code(422);
    echo json_encode(["success" => false, "message" => "Invalid order status update."]);
    exit;
}

mysqli_begin_transaction($conn);

try {

    // Lock this order's row and check its CURRENT status first, so we
    // know exactly which direction this change is going in.
    $currentStmt = mysqli_prepare($conn, "SELECT order_status FROM orders WHERE order_id = ? FOR UPDATE");
    mysqli_stmt_bind_param($currentStmt, "i", $order_id);
    mysqli_stmt_execute($currentStmt);
    $currentResult = mysqli_stmt_get_result($currentStmt);
    $currentRow = mysqli_fetch_assoc($currentResult);
    mysqli_free_result($currentResult);
    mysqli_stmt_close($currentStmt);

    if (!$currentRow) {
        throw new Exception("Order not found.");
    }

    $previousStatus = $currentRow["order_status"];

    if (in_array($previousStatus, ["Delivered", "Cancelled"], true) && $status !== $previousStatus) {
        throw new Exception("Delivered and cancelled orders are final and cannot be changed.");
    }

    // ---- CANCELLING: give stock back -----------------------------------
    // Only fires the moment an order actually transitions INTO Cancelled —
    // not on every re-save while it's already sitting at Cancelled, which
    // would keep adding stock back over and over.
    if ($status === "Cancelled" && $previousStatus !== "Cancelled") {

        $itemsStmt = mysqli_prepare($conn, "SELECT product_id, quantity FROM order_items WHERE order_id = ?");
        mysqli_stmt_bind_param($itemsStmt, "i", $order_id);
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

        $restoreStmt = mysqli_prepare($conn, "UPDATE products SET stock_quantity = stock_quantity + ? WHERE product_id = ?");
        foreach ($items as $item) {
            $qty = $item["quantity"];
            $productId = $item["product_id"];
            mysqli_stmt_bind_param($restoreStmt, "ii", $qty, $productId);
            mysqli_stmt_execute($restoreStmt);
        }

        mysqli_stmt_close($restoreStmt);
    }

    // ---- UN-CANCELLING: take stock back out, but only if there's ------
    // ---- enough of it left ---------------------------------------------
    // Symmetric case: moving OUT of Cancelled into any other status means
    // this order is live again, so its items need to come back out of
    // stock — the same stock that may have already been sold to someone
    // else in the meantime. If any item no longer has enough, the whole
    // status change is blocked (transaction rolls back, order stays
    // Cancelled) rather than allowing an oversell.
    if ($previousStatus === "Cancelled" && $status !== "Cancelled") {

        $itemsStmt = mysqli_prepare(
            $conn,
            "SELECT oi.product_id, oi.quantity, p.stock_quantity, p.card_name
             FROM order_items oi
             JOIN products p ON oi.product_id = p.product_id
             WHERE oi.order_id = ?
             FOR UPDATE"
        );
        mysqli_stmt_bind_param($itemsStmt, "i", $order_id);
        mysqli_stmt_execute($itemsStmt);
        $itemsResult = mysqli_stmt_get_result($itemsStmt);

        $items = [];
        while ($row = mysqli_fetch_assoc($itemsResult)) {
            $items[] = $row;
        }
        mysqli_stmt_close($itemsStmt);

        // Check every item BEFORE deducting anything — if even one is
        // short, we don't want to have already deducted the others.
        foreach ($items as $item) {
            if ((int) $item["quantity"] > (int) $item["stock_quantity"]) {
                throw new Exception(
                    "Can't restore this order — not enough stock left for \"" .
                    $item["card_name"] . "\" (need " . $item["quantity"] .
                    ", only " . $item["stock_quantity"] . " in stock)."
                );
            }
        }

        $deductStmt = mysqli_prepare($conn, "UPDATE products SET stock_quantity = stock_quantity - ? WHERE product_id = ?");

        foreach ($items as $item) {
            $qty = (int) $item["quantity"];
            $productId = (int) $item["product_id"];
            mysqli_stmt_bind_param($deductStmt, "ii", $qty, $productId);
            mysqli_stmt_execute($deductStmt);
        }

        mysqli_stmt_close($deductStmt);
    }

    $updateStmt = mysqli_prepare($conn, "UPDATE orders SET order_status = ? WHERE order_id = ?");
    mysqli_stmt_bind_param($updateStmt, "si", $status, $order_id);
    mysqli_stmt_execute($updateStmt);
    mysqli_stmt_close($updateStmt);

    mysqli_commit($conn);

    echo json_encode([
        "success" => true
    ]);

} catch (Throwable $e) {
    try {
        mysqli_rollback($conn);
    } catch (Throwable $rollbackError) {
        // Preserve the original error and return JSON instead of PHP error markup.
    }

    echo json_encode([
        "success" => false,
        "message" => $e->getMessage()
    ]);

}

mysqli_close($conn);

?>
