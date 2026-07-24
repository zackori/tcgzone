<?php

header("Content-Type: application/json");

include "../../../config/db_connect.php";

$sql = "

SELECT

    o.order_id, 

    CONCAT (u.first_name, ' ',u.last_name) AS customer_name,

    CONCAT (o.house_no_street, ', ', o.city) AS address1,

    CONCAT (o.province, ' ', o.zip_code) AS address2, 

    o.total_amount,

    o.payment_method,

    o.order_status AS status,

    o.order_date

FROM orders o


LEFT JOIN users u
ON o.user_id = u.id

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