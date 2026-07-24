<?php

header("Content-Type: application/json");

include("../../../config/db_connect.php");

// -------------------------------
// Monthly Orders
// -------------------------------

$orders = array_fill(0,12,0);

$sales = array_fill(0,12,0);

$sql1 = "

SELECT

MONTH(order_date) AS month,

COUNT(*) AS totalOrders,

SUM(total_amount) AS totalSales

FROM orders


GROUP BY MONTH(order_date)

ORDER BY MONTH(order_date)


";

$sql2 = "

SELECT

MONTH(order_date) AS month,

COUNT(*) AS totalOrders,

SUM(total_amount) AS totalSales

FROM orders

WHERE order_status = 'Delivered'

GROUP BY MONTH(order_date)

ORDER BY MONTH(order_date)

";
$result = $conn->query($sql1);

while($row = $result->fetch_assoc()){

    $index = $row["month"] - 1;

    $orders[$index] = (int)$row["totalOrders"];

}


$result = $conn->query($sql2);

while($row = $result->fetch_assoc()){

    $index = $row["month"] - 1;



    $sales[$index] = (float)$row["totalSales"];

}

// -------------------------------
// Current Product Stock
// -------------------------------

$stockLabels = [];

$stockValues = [];

$query = $conn->query("

SELECT card_name, stock_quantity

FROM products

ORDER BY card_name

");

while($row = $query->fetch_assoc()){

    $stockLabels[] = $row["card_name"];

    $stockValues[] = (int)$row["stock_quantity"];

}

echo json_encode([

    "orders"=>$orders,

    "sales"=>$sales,

    "stockLabels"=>$stockLabels,

    "stockValues"=>$stockValues

]);

$conn->close();

?>