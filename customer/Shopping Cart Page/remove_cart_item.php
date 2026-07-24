<?php
require '../../config/db_connect.php';
session_start();

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Not signed in.']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);
$cartItemId = (int)($data['cartItemId'] ?? 0);
$userId     = $_SESSION['user_id'];

if ($cartItemId <= 0) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid request.']);
    exit;
}

$delete = $conn->prepare(
    "DELETE ci FROM cart_items ci
     JOIN cart c ON ci.cart_id = c.cart_id
     WHERE ci.cart_item_id = ? AND c.user_id = ?"
);
$delete->bind_param("ii", $cartItemId, $userId);
$delete->execute();

echo json_encode(['success' => true, 'deleted' => $delete->affected_rows > 0]);