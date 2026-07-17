<?php
require '../../config/db_connect.php';

header('Content-Type: application/json');

$sql = "SELECT * FROM products_test";
$result = mysqli_query($conn, $sql);

$products = [];

while ($row = mysqli_fetch_assoc($result)) {

    $products[] = [
        "id" => "prod-" . $row["product_id"],

        "title" => $row["card_name"],

        "subtitle" => $row["set_name"],

        "image" => $row["image"],

        "price" => (float) $row["selling_price"],

        "marketPrice" => (float) $row["market_price"],

        "cardType" =>
            strtolower($row["product_type"]) == "cards"
            ? "singles"
            : "sealed",

        "rarity" =>
            strtolower(str_replace(' ', '-', $row["rarity"])),

        "condition" =>
            strtolower(str_replace(' ', '-', $row["condition"])),

        "stock" =>
            ($row["stock_quantity"] <= 0)
            ? "sold-out"
            : $row["stock_quantity"],

        "category" => $row["category"],


    ];
}

echo json_encode($products, JSON_PRETTY_PRINT);

mysqli_close($conn);