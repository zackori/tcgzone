<?php
header('Content-Type: application/json');
require '../../config/db_connect.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method.']);
    exit;
}

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'You must be signed in to submit a sell request.']);
    exit;
}

$user_id = $_SESSION['user_id'];
$action = $_POST['action'] ?? '';

if ($action !== 'submit_sell_request') {
    echo json_encode(['status' => 'error', 'message' => 'Unknown action.']);
    exit;
}

$cardName = trim($_POST['card_name'] ?? '');
$setName = trim($_POST['set_name'] ?? '');
$category = $_POST['category'] ?? '';
$productType = $_POST['product_type'] ?? '';
$rarity = $_POST['rarity'] ?? '';
$condition = $_POST['condition'] ?? '';
$sellingPrice = $_POST['selling_price'] ?? '';
$quantity = $_POST['quantity'] ?? '';
$notes = trim($_POST['notes'] ?? '');

if (empty($cardName) || empty($setName) || empty($category) || empty($productType) || empty($rarity) || empty($condition) || $sellingPrice === '' || $quantity === '') {
    echo json_encode(['status' => 'error', 'message' => 'Please fill in all required fields.']);
    exit;
}

if (!is_numeric($sellingPrice) || $sellingPrice < 0) {
    echo json_encode(['status' => 'error', 'message' => 'Selling price must be a valid number.']);
    exit;
}

if (!is_numeric($quantity) || (int) $quantity < 1) {
    echo json_encode(['status' => 'error', 'message' => 'Quantity must be at least 1.']);
    exit;
}

$imagePath = null;
if (!empty($_FILES['image']['name'])) {
    $image = $_FILES['image'];
    $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];

    if ($image['error'] !== UPLOAD_ERR_OK) {
        echo json_encode(['status' => 'error', 'message' => 'Image upload failed.']);
        exit;
    }

    if (!in_array($image['type'], $allowedTypes, true)) {
        echo json_encode(['status' => 'error', 'message' => 'Only JPG, PNG, and GIF images are allowed.']);
        exit;
    }

    $uploadDir = dirname(__DIR__, 2) . '/assets/images/sell';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }

    $filename = uniqid('sell_', true) . '_' . basename($image['name']);
    $targetPath = $uploadDir . '/' . $filename;

    if (!move_uploaded_file($image['tmp_name'], $targetPath)) {
        echo json_encode(['status' => 'error', 'message' => 'Unable to save image file.']);
        exit;
    }

    $imagePath = '/tcgzone/assets/images/sell/' . $filename;
}

$stmt = $conn->prepare(
    'INSERT INTO sell_requests (user_id, card_name, set_name, category, product_type, rarity, `condition`, selling_price, stock_quantity, image, notes, status, created_at)
     VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())'
);
$stmt->bind_param(
    'issssssidsis',
    $user_id,
    $cardName,
    $setName,
    $category,
    $productType,
    $rarity,
    $condition,
    $sellingPrice,
    $quantity,
    $imagePath,
    $notes,
    $status
);

$status = 'Pending';

if ($stmt->execute() === false) {
    echo json_encode(['status' => 'error', 'message' => 'Database error: ' . $stmt->error]);
    exit;
}

echo json_encode(['status' => 'ok']);
exit;
