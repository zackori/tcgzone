<?php
/* ============================================================
   UPDATE PRODUCT
   ------------------------------------------------------------
   NOTE: description isn't stored/updated here — it lives in
   products.json and is displayed read-only in the modal (see
   inventory.js). This endpoint only touches DB-backed fields.

   Image logic (three cases from the modal):
     1. Admin picked a new file       -> upload it, replace old path
     2. Admin clicked the × (remove)  -> imageRemoved=1, clear image
     3. Admin didn't touch the image  -> leave the existing path as-is
   ============================================================ */

session_start();
header('Content-Type: application/json');


require '../../../../config/db_connect.php';
require 'image_upload_helper.php'; // shared handleImageUpload() — see note below

$productId    = (int) str_replace('prod-', '', $_POST['productId'] ?? '');
$cardName     = trim($_POST['cardName'] ?? '');
$category     = trim($_POST['category'] ?? '');
$productType  = trim($_POST['productType'] ?? '');
$rarity       = trim($_POST['rarity'] ?? '');
$condition    = trim($_POST['condition'] ?? '');
$price        = (float)($_POST['price'] ?? 0);
$stock        = (int)($_POST['stockQuantity'] ?? 0);
$imageRemoved = ($_POST['imageRemoved'] ?? '0') === '1';

if ($productId <= 0 || $cardName === '' || $category === '' || $productType === '' || $rarity === '' || $condition === '' || $price <= 0) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Please fill in card name, category, product type, rarity, condition, and a valid price.']);
    exit;
}

// ---- Current image path, so we know what (if anything) to delete from disk ----
$currentStmt = $conn->prepare("SELECT image FROM products WHERE product_id = ?");
$currentStmt->bind_param("i", $productId);
$currentStmt->execute();
$currentRow = $currentStmt->get_result()->fetch_assoc();

if (!$currentRow) {
    http_response_code(404);
    echo json_encode(['success' => false, 'message' => 'Product not found.']);
    exit;
}

$imagePath = $currentRow['image']; // default: keep whatever's already there

if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
    // Case 1: a new file was chosen
    $uploadResult = handleImageUpload($_FILES['image']);
    if (!$uploadResult['success']) {
        http_response_code(400);
        echo json_encode($uploadResult);
        exit;
    }
    deleteProductImageFile($currentRow['image']);
    $imagePath = $uploadResult['path'];

} elseif ($imageRemoved) {
    // Case 2: the × button was clicked and no replacement was chosen
    deleteProductImageFile($currentRow['image']);
    $imagePath = null;
}
// else Case 3: nothing changed, $imagePath already holds the existing value

$stmt = $conn->prepare(
    "UPDATE products
     SET category = ?, card_name = ?, product_type = ?, rarity = ?, `condition` = ?, selling_price = ?, stock_quantity = ?, image = ?
     WHERE product_id = ?"
);
$stmt->bind_param("sssssdisi", $category, $cardName, $productType, $rarity, $condition, $price, $stock, $imagePath, $productId);
$stmt->execute();

echo json_encode(['success' => true]);
