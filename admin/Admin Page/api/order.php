<?php

header("Content-Type: application/json");

include "../../../config/db_connect.php";

$sql = "

SELECT

    o.id AS order_id,

    o.customer_name,

    p.product_name AS pokemon_name,

    o.quantity,

    o.total AS total_amount,

    o.status,

    o.order_date

FROM orders_admin o

LEFT JOIN products p
ON o.product_id = p.id

ORDER BY o.order_date DESC

";

$result = mysqli_query($conn, $sql);

if (!$result) {
    die(mysqli_error($conn));
}

$orders = [];

while ($row = mysqli_fetch_assoc($result)) {

    $orders[] = $row;

}

echo json_encode($orders);

?>