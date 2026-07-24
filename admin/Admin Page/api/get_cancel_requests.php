<?php

header("Content-Type: application/json");

include "../../../config/db_connect.php";

$sql = "
SELECT
    cr.cancel_request_id,
    cr.order_id,
    cr.reason,
    cr.status,
    cr.requested_at,
    CONCAT(u.first_name, ' ', u.last_name) AS customer_name,
    o.total_amount,
    o.order_status
FROM cancel_requests cr
JOIN orders o ON cr.order_id = o.order_id
LEFT JOIN users u ON o.user_id = u.id
ORDER BY cr.requested_at DESC
";

$result = mysqli_query($conn, $sql);

if (!$result) {
    echo json_encode([
        "success" => false,
        "message" => mysqli_error($conn)
    ]);
    exit;
}

$requests = [];

while ($row = mysqli_fetch_assoc($result)) {
    $requests[] = $row;
}

echo json_encode([
    "success" => true,
    "requests" => $requests
]);

mysqli_close($conn);
