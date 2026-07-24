<?php
header("Content-Type: application/json; charset=utf-8");
require_once __DIR__ . "/../../../config/db_connect.php";

$productsResult = mysqli_query($conn, "SELECT product_id, card_name, set_name, category, product_type, rarity, `condition`, stock_quantity, market_price FROM products WHERE stock_quantity <= 3 ORDER BY stock_quantity ASC, card_name ASC");
$products = [];
while ($row = mysqli_fetch_assoc($productsResult)) $products[] = $row;

$suppliersResult = mysqli_query($conn, "SELECT supplier_id, supplier_name FROM suppliers ORDER BY supplier_name");
$suppliers = [];
while ($row = mysqli_fetch_assoc($suppliersResult)) $suppliers[] = $row;
mysqli_close($conn);
echo json_encode(["success" => true, "products" => $products, "suppliers" => $suppliers]);
