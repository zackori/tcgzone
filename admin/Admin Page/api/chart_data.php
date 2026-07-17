<?php

header("Content-Type: application/json");

include("../../../config/db_connect.php");

// -------------------------------
// Monthly Orders
// -------------------------------

$orders = array_fill(0,12,0);

$sales = array_fill(0,12,0);

$sql = "

SELECT

MONTH(order_date) AS month,

COUNT(*) AS totalOrders,

SUM(total) AS totalSales

FROM orders_admin

GROUP BY MONTH(order_date)

ORDER BY MONTH(order_date)

";

$result = $conn->query($sql);

while($row = $result->fetch_assoc()){

    $index = $row["month"] - 1;

    $orders[$index] = (int)$row["totalOrders"];

    $sales[$index] = (float)$row["totalSales"];

}

// -------------------------------
// Current Product Stock
// -------------------------------

$stockLabels = [];

$stockValues = [];

$query = $conn->query("

SELECT product_name, stock

FROM products_admin

ORDER BY product_name

");

while($row = $query->fetch_assoc()){

    $stockLabels[] = $row["product_name"];

    $stockValues[] = (int)$row["stock"];

}

echo json_encode([

    "orders"=>$orders,

    "sales"=>$sales,

    "stockLabels"=>$stockLabels,

    "stockValues"=>$stockValues

]);

$conn->close();

?>