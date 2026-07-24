<?php
session_start();
header('Content-Type: application/json');


require '../../../../config/db_connect.php';
require 'image_upload_helper.php';

$data = json_decode(file_get_contents('php://input'), true);
$productId = (int) str_replace('prod-', '', $data['productId'] ?? '');

if ($productId <= 0) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid product.']);
    exit;
}

// ---- Look up the image path + name before deleting the row ----
$stmt = $conn->prepare("SELECT card_name, image FROM products WHERE product_id = ?");
$stmt->bind_param("i", $productId);
$stmt->execute();
$row = $stmt->get_result()->fetch_assoc();

if (!$row) {
    http_response_code(404);
    echo json_encode(['success' => false, 'message' => 'Product not found.']);
    exit;
}

$deleteStmt = $conn->prepare("DELETE FROM products WHERE product_id = ?");
$deleteStmt->bind_param("i", $productId);
$deleteStmt->execute();

// ---- Clean up the uploaded image file, if there was one ----
deleteProductImageFile($row['image']);

echo json_encode(['success' => true, 'cardName' => $row['card_name']]);
