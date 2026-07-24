<?php

header("Content-Type: application/json");

include "../../../config/db_connect.php";

/*
|--------------------------------------------------------------------------
| Cards
|--------------------------------------------------------------------------
*/

$cards = mysqli_query($conn, "

SELECT

COALESCE(SUM(total_amount),0) AS revenue,

COALESCE(ROUND(AVG(total_amount),2), 2) AS averageOrder,

COUNT(*) AS totalOrders,

SUM(order_status='Delivered') AS completedOrders

FROM orders

WHERE order_status='Delivered'

");

$card = mysqli_fetch_assoc($cards);

/*
|--------------------------------------------------------------------------
| Revenue Per Month
|--------------------------------------------------------------------------
*/

$revenueData = array_fill(0,12,0);

$result = mysqli_query($conn, "

SELECT

MONTH(order_date) AS month,

SUM(total_amount) AS revenue

FROM orders

WHERE order_status='Delivered'

GROUP BY MONTH(order_date)

ORDER BY MONTH(order_date)




");

while($row=mysqli_fetch_assoc($result)){

    $index = $row["month"] - 1;

    $revenueData[$index] = (float)$row["revenue"];

}

/*
|--------------------------------------------------------------------------
| Orders Per Month
|--------------------------------------------------------------------------
*/

$orderData = array_fill(0,12,0);

$result = mysqli_query($conn, "

SELECT

MONTH(order_date) AS month,

COUNT(*) AS totalOrders

FROM orders

WHERE order_status='Delivered'

GROUP BY MONTH(order_date)

ORDER BY MONTH(order_date)

");

while($row=mysqli_fetch_assoc($result)){

    $index = $row["month"]-1;

    $orderData[$index]=(int)$row["totalOrders"];

}

/*
|--------------------------------------------------------------------------
| Profit
|--------------------------------------------------------------------------
|
| Temporary:
| Profit = 35% of revenue
|
| Later we can replace this with actual product cost.
|
*/

$profitData=[];

foreach($revenueData as $value){

    $profitData[] = round($value * .35,2);

}

/*
|--------------------------------------------------------------------------
| Response
|--------------------------------------------------------------------------
*/

echo json_encode([

"cards"=>[

"revenue"=>$card["revenue"],

"average"=>$card["averageOrder"],

"completed"=>$card["completedOrders"],

"orders"=>$card["totalOrders"]

],

"revenue"=>$revenueData,

"profit"=>$profitData,

"orders"=>$orderData

]);

?>