<?php
require '../../config/db_connect.php';

header('Content-Type: application/json');

$sql = "SELECT * FROM products";
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
            // Passed through exactly as stored. The old version forced
            // this to only ever be "singles" or "sealed" — that broke
            // the moment Card Type became category-specific (Magic's
            // "Creature"/"Instant", One Piece's "Character"/"Leader",
            // etc. all need their real value to survive intact).
            $row["product_type"],

        "rarity" =>
            // Pokémon's rarity dropdown still uses the old lowercase-
            // hyphenated style ("ultra-rare"), so that one category
            // keeps the slugify transform. Magic/One Piece use short
            // uppercase codes ("SEC", "M") that must pass through raw —
            // lowercasing them would break the match entirely.
            ($row["category"] === 'Pokémon')
                ? strtolower(str_replace(' ', '-', $row["rarity"]))
                : $row["rarity"],

        "condition" =>
            strtolower(str_replace(' ', '-', $row["condition"])),

        // Keep the original database value for display in the product modal.
        // The slug above is still used by the condition filter.
        "conditionLabel" => $row["condition"],

        "stock" => (int)$row["stock_quantity"],

        "category" => $row["category"],


    ];
}

echo json_encode($products, JSON_PRETTY_PRINT);

mysqli_close($conn);
