<?php
header("Content-Type: application/json; charset=utf-8");
require_once __DIR__ . "/../../../config/db_connect.php";

$cards = json_decode($_POST["cards"] ?? "", true);
if (!is_array($cards) || count($cards) === 0) {
    http_response_code(422);
    echo json_encode(["success" => false, "message" => "Add at least one card."]);
    exit;
}

$categories = ["Pokémon", "Magic: The Gathering", "One Piece"];
$productTypes = ["Cards", "Sealed", "Collections", "Character", "Leader", "Artifact", "Legendary Creature", "Legendary Artifact", "Enchantment", "Instant", "Creature"];
$rarities = ["", "Common", "Uncommon", "Rare", "Ultra Rare", "Secret Rare", "C", "UC", "R", "SR", "SEC", "L", "P", "SP", "AA", "TR", "MR", "M", "S", "U"];
$conditions = ["", "Mint", "Near Mint", "Lightly Played", "Damaged"];
$uploadDirectory = dirname(__DIR__, 3) . "/assets/images/products";
if (!is_dir($uploadDirectory) && !mkdir($uploadDirectory, 0755, true)) {
    http_response_code(500); echo json_encode(["success" => false, "message" => "Could not create the image directory."]); exit;
}

$uploadedFiles = [];
mysqli_begin_transaction($conn);
try {
    $insertStmt = mysqli_prepare($conn, "INSERT INTO products (category, card_name, product_type, set_name, rarity, `condition`, selling_price, product_cost, market_price, stock_quantity, image) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $createdIds = [];
    $receiptItems = [];
    $totalAmount = 0.0;

    foreach ($cards as $index => $card) {
        $cardName = trim($card["card_name"] ?? "");
        $setName = trim($card["set_name"] ?? "");
        $category = $card["category"] ?? "";
        $productType = $card["product_type"] ?? "";
        $rarity = $card["rarity"] ?? "";
        $condition = $card["condition"] ?? "";
        $sellingPrice = filter_var($card["selling_price"] ?? null, FILTER_VALIDATE_FLOAT);
        $productCost = filter_var($card["product_cost"] ?? null, FILTER_VALIDATE_FLOAT);
        $marketPrice = filter_var($card["market_price"] ?? null, FILTER_VALIDATE_FLOAT);
        $quantity = filter_var($card["stock_quantity"] ?? null, FILTER_VALIDATE_INT);

        if ($cardName === "" || $setName === "" || !in_array($category, $categories, true) || !in_array($productType, $productTypes, true) || !in_array($rarity, $rarities, true) || !in_array($condition, $conditions, true) || $sellingPrice === false || $productCost === false || $marketPrice === false || $quantity === false || $sellingPrice < 0 || $productCost < 0 || $marketPrice < 0 || $quantity < 0) {
            throw new RuntimeException("Card " . ($index + 1) . " has incomplete or invalid information.");
        }

        $fileKey = "image_" . $index;
        if (!isset($_FILES[$fileKey]) || $_FILES[$fileKey]["error"] !== UPLOAD_ERR_OK) {
            throw new RuntimeException("Add an image for card " . ($index + 1) . ".");
        }
        $image = $_FILES[$fileKey];
        $mimeType = mime_content_type($image["tmp_name"]);
        $extensions = ["image/jpeg" => "jpg", "image/png" => "png", "image/gif" => "gif", "image/webp" => "webp"];
        if (!isset($extensions[$mimeType])) throw new RuntimeException("Card " . ($index + 1) . " must use a JPG, PNG, GIF, or WEBP image.");
        if ($image["size"] > 5 * 1024 * 1024) throw new RuntimeException("Card " . ($index + 1) . " image must be 5 MB or smaller.");

        $filename = "purchased_" . uniqid("", true) . "." . $extensions[$mimeType];
        $diskPath = $uploadDirectory . "/" . $filename;
        if (!move_uploaded_file($image["tmp_name"], $diskPath)) throw new RuntimeException("Could not save the image for card " . ($index + 1) . ".");
        $uploadedFiles[] = $diskPath;
        $imagePath = "/tcgzone/assets/images/products/" . $filename;

        mysqli_stmt_bind_param($insertStmt, "ssssssdddis", $category, $cardName, $productType, $setName, $rarity, $condition, $sellingPrice, $productCost, $marketPrice, $quantity, $imagePath);
        mysqli_stmt_execute($insertStmt);
        $productId = mysqli_insert_id($conn);
        $createdIds[] = $productId;
        $subtotal = $productCost * $quantity;
        $receiptItems[] = [$productId, $quantity, $productCost, $subtotal];
        $totalAmount += $subtotal;
    }
    mysqli_stmt_close($insertStmt);

    // Direct purchases are immediately owned by TCGZONE and already in stock,
    // so their receipt starts as Delivered and won't trigger resupply twice.
    $supplierStmt = mysqli_prepare($conn, "INSERT INTO suppliers (supplier_name) VALUES ('TCGZONE') ON DUPLICATE KEY UPDATE supplier_id = LAST_INSERT_ID(supplier_id)");
    mysqli_stmt_execute($supplierStmt);
    $supplierId = mysqli_insert_id($conn);
    mysqli_stmt_close($supplierStmt);

    $receiptStmt = mysqli_prepare($conn, "INSERT INTO procurement_orders (supplier_id, total_amount, order_status) VALUES (?, ?, 'Delivered')");
    mysqli_stmt_bind_param($receiptStmt, "id", $supplierId, $totalAmount);
    mysqli_stmt_execute($receiptStmt);
    $receiptId = mysqli_insert_id($conn);
    mysqli_stmt_close($receiptStmt);

    $receiptItemStmt = mysqli_prepare($conn, "INSERT INTO procurement_order_items (procurement_order_id, product_id, quantity, unit_cost, subtotal) VALUES (?, ?, ?, ?, ?)");
    foreach ($receiptItems as [$productId, $quantity, $unitCost, $subtotal]) {
        mysqli_stmt_bind_param($receiptItemStmt, "iiidd", $receiptId, $productId, $quantity, $unitCost, $subtotal);
        mysqli_stmt_execute($receiptItemStmt);
    }
    mysqli_stmt_close($receiptItemStmt);

    mysqli_commit($conn);
    echo json_encode(["success" => true, "product_ids" => $createdIds, "procurement_order_id" => $receiptId]);
} catch (Throwable $error) {
    mysqli_rollback($conn);
    foreach ($uploadedFiles as $file) if (is_file($file)) unlink($file);
    http_response_code(422);
    echo json_encode(["success" => false, "message" => $error->getMessage()]);
}
mysqli_close($conn);
