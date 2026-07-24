<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

require '../../config/db_connect.php';
session_start();

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Not signed in.']);
    exit;
}

$userId = $_SESSION['user_id'];

$stmt = $conn->prepare(
    "SELECT ci.cart_item_id, ci.product_id, ci.quantity,
            p.card_name, p.image, p.selling_price, p.stock_quantity
     FROM cart_items ci
     JOIN cart c ON ci.cart_id = c.cart_id
     JOIN products p ON ci.product_id = p.product_id
     WHERE c.user_id = ?"
);
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();

$items = [];
while ($row = $result->fetch_assoc()) {
    $items[] = [
        "cartItemId"    => (int)$row['cart_item_id'],
        "productId"     => "prod-" . $row['product_id'],
        "name"          => $row['card_name'],
        "price"         => (float)$row['selling_price'],
        "quantity"      => (int)$row['quantity'],
        "image"         => $row['image'],
        "stockQuantity" => (int)$row['stock_quantity']
    ];
}

echo json_encode(['success' => true, 'items' => $items]);