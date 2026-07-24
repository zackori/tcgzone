<?php
require '../../config/db_connect.php';
session_start();

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'You must be signed in.']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);
$orderId = isset($data['order_id']) ? (int) $data['order_id'] : 0;
$reason = isset($data['reason']) ? trim($data['reason']) : '';

$allowedReasons = [
    'Better Price Elsewhere',
    'Unforeseen Financial Circumstances',
    'Emergency/Unexpected Changes'
];

if ($orderId <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid order.']);
    exit;
}

if (!in_array($reason, $allowedReasons, true)) {
    echo json_encode(['success' => false, 'message' => 'Please select a valid cancellation reason.']);
    exit;
}

$userId = (int) $_SESSION['user_id'];

$orderStmt = $conn->prepare(
    "SELECT order_status FROM orders WHERE order_id = ? AND user_id = ?"
);
$orderStmt->bind_param('ii', $orderId, $userId);
$orderStmt->execute();
$orderRow = $orderStmt->get_result()->fetch_assoc();
$orderStmt->close();

if (!$orderRow) {
    echo json_encode(['success' => false, 'message' => 'Order not found.']);
    exit;
}

if ($orderRow['order_status'] !== 'Pending') {
    echo json_encode(['success' => false, 'message' => 'Only pending orders can be cancelled.']);
    exit;
}

$existingStmt = $conn->prepare(
    "SELECT cancel_request_id, status FROM cancel_requests WHERE order_id = ?"
);
$existingStmt->bind_param('i', $orderId);
$existingStmt->execute();
$existingRow = $existingStmt->get_result()->fetch_assoc();
$existingStmt->close();

if ($existingRow) {
    if ($existingRow['status'] === 'Pending') {
        echo json_encode(['success' => false, 'message' => 'A cancellation request is already pending for this order.']);
        exit;
    }

    if ($existingRow['status'] === 'Approved') {
        echo json_encode(['success' => false, 'message' => 'This order has already been cancelled.']);
        exit;
    }

    $updateStmt = $conn->prepare(
        "UPDATE cancel_requests
         SET reason = ?, status = 'Pending', requested_at = CURRENT_TIMESTAMP
         WHERE order_id = ?"
    );
    $updateStmt->bind_param('si', $reason, $orderId);

    if ($updateStmt->execute()) {
        echo json_encode([
            'success' => true,
            'message' => 'Cancellation request submitted. An admin will review it shortly.'
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Could not submit cancellation request.']);
    }

    $updateStmt->close();
    mysqli_close($conn);
    exit;
}

$insertStmt = $conn->prepare(
    "INSERT INTO cancel_requests (order_id, reason, status) VALUES (?, ?, 'Pending')"
);
$insertStmt->bind_param('is', $orderId, $reason);

if ($insertStmt->execute()) {
    echo json_encode([
        'success' => true,
        'message' => 'Cancellation request submitted. An admin will review it shortly.'
    ]);
} else {
    echo json_encode(['success' => false, 'message' => 'Could not submit cancellation request.']);
}

$insertStmt->close();
mysqli_close($conn);
