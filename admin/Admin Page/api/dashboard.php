<?php

include("../../../config/db_connect.php");

$response=[];

/* Total Orders */

$sql="SELECT COUNT(*) totalOrders FROM orders_admin";

$result=$conn->query($sql);

$row=$result->fetch_assoc();

$response['orders']=$row['totalOrders'];

/* Total Sales */

$sql="SELECT SUM(total) totalSales FROM orders_admin";

$result=$conn->query($sql);

$row=$result->fetch_assoc();

$response['sales']=$row['totalSales'] ?? 0;

/* Out of Stock */

$sql="SELECT COUNT(*) outStock FROM products_admin WHERE stock=0";

$result=$conn->query($sql);

$row=$result->fetch_assoc();

$response['stock']=$row['outStock'];

echo json_encode($response);

?>