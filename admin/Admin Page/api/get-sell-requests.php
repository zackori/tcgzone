<?php
header("Content-Type: application/json; charset=utf-8");

require_once __DIR__ . "/../../../config/db_connect.php";

$sql = "
    SELECT
        sr.request_id,
        sr.product_id,
        sr.card_name,
        sr.set_name,
        sr.category,
        sr.product_type,
        sr.rarity,
        sr.`condition`,
        sr.selling_price,
        sr.stock_quantity AS quantity,
        sr.image,
        sr.notes,
        sr.status,
        sr.created_at,
        u.username,
        CASE 
            WHEN sr.status = 'Approved' AND p.product_id IS NOT NULL THEN p.product_cost
            ELSE sr.selling_price
        END AS approved_product_cost
    FROM sell_requests sr
    LEFT JOIN users u ON sr.user_id = u.id
    LEFT JOIN products p ON sr.product_id = p.product_id
    ORDER BY sr.created_at DESC, sr.request_id DESC
";

$result = mysqli_query($conn, $sql);

if (!$result) {
    http_response_code(500);
    echo json_encode(["error" => "Could not load sell requests."]);
    exit;
}

$requests = [];
while ($row = mysqli_fetch_assoc($result)) {
    $requests[] = $row;
}

mysqli_close($conn);
echo json_encode($requests);
