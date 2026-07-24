<?php

header("Content-Type: application/json");

include "../../../config/db_connect.php";

$data = json_decode(file_get_contents("php://input"), true);

$cancelRequestId = isset($data["cancel_request_id"]) ? (int) $data["cancel_request_id"] : 0;
$action = isset($data["action"]) ? trim($data["action"]) : "";

if ($cancelRequestId <= 0 || !in_array($action, ["approve", "reject"], true)) {
    echo json_encode([
        "success" => false,
        "message" => "Invalid request."
    ]);
    exit;
}

mysqli_begin_transaction($conn);

try {

    $requestStmt = mysqli_prepare(
        $conn,
        "SELECT cr.cancel_request_id, cr.order_id, cr.status, o.order_status
         FROM cancel_requests cr
         JOIN orders o ON cr.order_id = o.order_id
         WHERE cr.cancel_request_id = ?
         FOR UPDATE"
    );
    mysqli_stmt_bind_param($requestStmt, "i", $cancelRequestId);
    mysqli_stmt_execute($requestStmt);
    $requestResult = mysqli_stmt_get_result($requestStmt);
    $requestRow = mysqli_fetch_assoc($requestResult);
    mysqli_stmt_close($requestStmt);

    if (!$requestRow) {
        throw new Exception("Cancel request not found.");
    }

    if ($requestRow["status"] !== "Pending") {
        throw new Exception("This cancel request has already been reviewed.");
    }

    $orderId = (int) $requestRow["order_id"];
    $newRequestStatus = $action === "approve" ? "Approved" : "Rejected";

    if ($action === "approve") {

        if ($requestRow["order_status"] !== "Pending") {
            throw new Exception("Only pending orders can be cancelled.");
        }

        $itemsStmt = mysqli_prepare($conn, "SELECT product_id, quantity FROM order_items WHERE order_id = ?");
        mysqli_stmt_bind_param($itemsStmt, "i", $orderId);
        mysqli_stmt_execute($itemsStmt);
        $itemsResult = mysqli_stmt_get_result($itemsStmt);

        $restoreStmt = mysqli_prepare($conn, "UPDATE products SET stock_quantity = stock_quantity + ? WHERE product_id = ?");

        while ($item = mysqli_fetch_assoc($itemsResult)) {
            $qty = (int) $item["quantity"];
            $productId = (int) $item["product_id"];
            mysqli_stmt_bind_param($restoreStmt, "ii", $qty, $productId);
            mysqli_stmt_execute($restoreStmt);
        }

        mysqli_stmt_close($itemsStmt);
        mysqli_stmt_close($restoreStmt);

        $orderUpdateStmt = mysqli_prepare($conn, "UPDATE orders SET order_status = 'Cancelled' WHERE order_id = ?");
        mysqli_stmt_bind_param($orderUpdateStmt, "i", $orderId);
        mysqli_stmt_execute($orderUpdateStmt);
        mysqli_stmt_close($orderUpdateStmt);
    }

    $requestUpdateStmt = mysqli_prepare(
        $conn,
        "UPDATE cancel_requests SET status = ? WHERE cancel_request_id = ?"
    );
    mysqli_stmt_bind_param($requestUpdateStmt, "si", $newRequestStatus, $cancelRequestId);
    mysqli_stmt_execute($requestUpdateStmt);
    mysqli_stmt_close($requestUpdateStmt);

    mysqli_commit($conn);

    echo json_encode([
        "success" => true,
        "status" => $newRequestStatus
    ]);

} catch (Throwable $e) {

    mysqli_rollback($conn);

    echo json_encode([
        "success" => false,
        "message" => $e->getMessage()
    ]);

}

mysqli_close($conn);
