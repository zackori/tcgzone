<?php
require '../../config/db_connect.php';
session_start();

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'You must be signed in to add items to your cart.']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);

$rawProductId = trim($data['productId'] ?? '');   // e.g. "prod-12"
$quantity     = (int)($data['quantity'] ?? 1);
$productId    = (int) str_replace('prod-', '', $rawProductId);
$userId       = $_SESSION['user_id'];

if ($productId <= 0 || $quantity < 1) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid product or quantity.']);
    exit;
}

// ---- Real stock, from the products table ----
$stockStmt = $conn->prepare("SELECT stock_quantity FROM products WHERE product_id = ?");
$stockStmt->bind_param("i", $productId);
$stockStmt->execute();
$stockResult = $stockStmt->get_result()->fetch_assoc();

if (!$stockResult) {
    http_response_code(404);
    echo json_encode(['success' => false, 'message' => 'Product not found.']);
    exit;
}
$availableStock = (int)$stockResult['stock_quantity'];

// ---- Get this user's cart, or create one if they don't have one yet ----
$cartStmt = $conn->prepare("SELECT cart_id FROM cart WHERE user_id = ?");
$cartStmt->bind_param("i", $userId);
$cartStmt->execute();
$cartRow = $cartStmt->get_result()->fetch_assoc();

if ($cartRow) {
    $cartId = (int)$cartRow['cart_id'];
} else {
    $createCart = $conn->prepare("INSERT INTO cart (user_id, created_at, updated_at) VALUES (?, NOW(), NOW())");
    $createCart->bind_param("i", $userId);
    $createCart->execute();
    $cartId = $createCart->insert_id;
}

// ---- How much of this product is already in the cart? ----
$existingStmt = $conn->prepare("SELECT cart_item_id, quantity FROM cart_items WHERE cart_id = ? AND product_id = ?");
$existingStmt->bind_param("ii", $cartId, $productId);
$existingStmt->execute();
$existingRow = $existingStmt->get_result()->fetch_assoc();
$currentQtyInCart = $existingRow ? (int)$existingRow['quantity'] : 0;

if ($currentQtyInCart + $quantity > $availableStock) {
    http_response_code(409);

    if ($currentQtyInCart >= $availableStock) {
        
        echo json_encode([
            'success' => false,
            'message' => 'You already have the maximum available quantity (' . $availableStock . ') of this item in your cart.'
        ]);
    } else {
        
        $remaining = $availableStock - $currentQtyInCart;
        echo json_encode([
            'success' => false,
            'message' => 'Only ' . $remaining . ' more of this item can be added to your cart (stock limit: ' . $availableStock . ').'
        ]);
    }
    exit;
}

// ---- Insert the line, or bump quantity if it's already there ----
if ($existingRow) {
    $update = $conn->prepare("UPDATE cart_items SET quantity = quantity + ? WHERE cart_item_id = ?");
    $update->bind_param("ii", $quantity, $existingRow['cart_item_id']);
    $update->execute();
} else {
    $insert = $conn->prepare("INSERT INTO cart_items (cart_id, product_id, quantity) VALUES (?, ?, ?)");
    $insert->bind_param("iii", $cartId, $productId, $quantity);
    $insert->execute();
}

$touchCart = $conn->prepare("UPDATE cart SET updated_at = NOW() WHERE cart_id = ?");
$touchCart->bind_param("i", $cartId);
$touchCart->execute();

// ---- Total item count across the cart, for the nav badge ----
$countStmt = $conn->prepare(
    "SELECT SUM(ci.quantity) AS total
     FROM cart_items ci
     JOIN cart c ON ci.cart_id = c.cart_id
     WHERE c.user_id = ?"
);
$countStmt->bind_param("i", $userId);
$countStmt->execute();
$cartCount = (int)($countStmt->get_result()->fetch_assoc()['total'] ?? 0);

echo json_encode(['success' => true, 'cartCount' => $cartCount]);