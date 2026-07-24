<?php

header("Content-Type: application/json");

include "../../../config/db_connect.php";

$sql = "
SELECT
    u.id,
    u.username,
    TRIM(CONCAT(COALESCE(u.first_name, ''), ' ', COALESCE(u.last_name, ''))) AS name,
    u.email,
    u.phone,
    CONCAT_WS(', ', NULLIF(u.address_details, ''), NULLIF(u.address_city, ''),
        NULLIF(u.address_province, ''), NULLIF(u.address_zip, '')) AS address,
    CASE WHEN pending_orders.user_id IS NULL THEN 0 ELSE 1 END AS has_pending_order
FROM users u
LEFT JOIN (
    SELECT DISTINCT user_id
    FROM orders
    WHERE order_status = 'Pending'
) AS pending_orders ON pending_orders.user_id = u.id
ORDER BY u.id DESC
";

$result = mysqli_query($conn, $sql);

if (!$result) {
    http_response_code(500);
    echo json_encode(["error" => "Could not load users."]);
    exit;
}

$users = [];

while ($row = mysqli_fetch_assoc($result)) {

    $users[] = $row;

}

echo json_encode($users);

?>
