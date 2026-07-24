<?php
/* ============================================================
   ADD PRODUCT
   ------------------------------------------------------------
   ASSUMPTIONS FLAGGED (your `products` table has NOT NULL
   columns this form doesn't collect, since the mockup only
   shows Card Name / Category / Price / Stock / Description):
     - product_type -> defaulted to 'Cards'
     - set_name     -> defaulted to '' (empty string, allowed by VARCHAR)
     - rarity       -> defaulted to '' (allowed — it's in the enum)
     - condition    -> defaulted to '' (allowed — it's in the enum)
   Adjust the defaults below, or add real form fields for these
   later if you want admins to set them per-card.

   Description isn't handled here — it lives in products.json,
   not the database (see inventory.js).
   ============================================================ */

session_start();
header('Content-Type: application/json');


require '../../../../config/db_connect.php';
require 'image_upload_helper.php';

$cardName    = trim($_POST['cardName'] ?? '');
$category    = trim($_POST['category'] ?? '');
$price       = (float)($_POST['price'] ?? 0);
$stock       = (int)($_POST['stockQuantity'] ?? 0);

if ($cardName === '' || $category === '' || $price <= 0) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Please fill in card name, category, and a valid price.']);
    exit;
}

// ---- Image upload (optional) ----
$imagePath = null;
if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
    $uploadResult = handleImageUpload($_FILES['image']);
    if (!$uploadResult['success']) {
        http_response_code(400);
        echo json_encode($uploadResult);
        exit;
    }
    $imagePath = $uploadResult['path'];
}

$defaultProductType = 'Cards';
$defaultSetName     = '';
$defaultRarity       = '';
$defaultCondition    = '';

$stmt = $conn->prepare(
    "INSERT INTO products
        (category, card_name, product_type, set_name, rarity, `condition`,
         selling_price, stock_quantity, image, created_at)
     VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())"
);
$stmt->bind_param(
    "ssssssdis",
    $category, $cardName, $defaultProductType, $defaultSetName,
    $defaultRarity, $defaultCondition, $price, $stock, $imagePath
);
$stmt->execute();

echo json_encode(['success' => true, 'productId' => 'prod-' . $stmt->insert_id]);
