<?php
session_start();
header('Content-Type: application/json');
require '../../../../config/db_connect.php';

$data = json_decode(file_get_contents('php://input'), true);
$productId = (int) str_replace('prod-', '', $data['productId'] ?? '');
$action = ($data['action'] ?? 'archive') === 'restore' ? 'restore' : 'archive';

if ($productId <= 0) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid product.']);
    exit;
}

$stmt = $conn->prepare("SELECT card_name, is_archived FROM products WHERE product_id = ?");
$stmt->bind_param("i", $productId);
$stmt->execute();
$product = $stmt->get_result()->fetch_assoc();

if (!$product || ($action === 'archive' && (int)$product['is_archived'] === 1) || ($action === 'restore' && (int)$product['is_archived'] === 0)) {
    http_response_code(404);
    echo json_encode(['success' => false, 'message' => $action === 'restore' ? 'Product not found or already active.' : 'Product not found or already archived.']);
    exit;
}

$archivedValue = $action === 'restore' ? 0 : 1;
$statusStmt = $conn->prepare("UPDATE products SET is_archived = ? WHERE product_id = ?");
$statusStmt->bind_param("ii", $archivedValue, $productId);
$statusStmt->execute();

echo json_encode(['success' => true, 'cardName' => $product['card_name'], 'archived' => $archivedValue === 1, 'restored' => $archivedValue === 0]);
