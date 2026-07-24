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
$quantity   = (int)($data['quantity'] ?? 0);
$userId     = $_SESSION['user_id'];

if ($cartItemId <= 0 || $quantity < 1) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid request.']);
    exit;
}

// Ownership check: only touch a cart_item that actually belongs to this user's cart
$ownerStmt = $conn->prepare(
    "SELECT ci.product_id, p.stock_quantity
     FROM cart_items ci
     JOIN cart c ON ci.cart_id = c.cart_id
     JOIN products p ON ci.product_id = p.product_id
     WHERE ci.cart_item_id = ? AND c.user_id = ?"
);
$ownerStmt->bind_param("ii", $cartItemId, $userId);
$ownerStmt->execute();
$row = $ownerStmt->get_result()->fetch_assoc();

if (!$row) {
    http_response_code(404);
    echo json_encode(['success' => false, 'message' => 'Cart item not found.']);
    exit;
}

if ($quantity > (int)$row['stock_quantity']) {
    echo json_encode([
        'success' => false,
        'message' => 'You already have the maximum available quantity (' . $row['stock_quantity'] . ') of this item in your cart.'
    ]);
    exit;
}

$update = $conn->prepare("UPDATE cart_items SET quantity = ? WHERE cart_item_id = ?");
$update->bind_param("ii", $quantity, $cartItemId);
$update->execute();

echo json_encode(['success' => true]);