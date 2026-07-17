<?php

header("Content-Type: application/json");

include "../../../config/db_connect.php";

$data=json_decode(file_get_contents("php://input"),true);

$order_id=$data["order_id"];
$status=$data["status"];

$sql="UPDATE orders
SET status=?
WHERE id=?";

$stmt=mysqli_prepare($conn,$sql);

mysqli_stmt_bind_param($stmt,"si",$status,$order_id);

if(mysqli_stmt_execute($stmt)){

    echo json_encode([
        "success"=>true
    ]);

}else{

    echo json_encode([
        "success"=>false,
        "message"=>"Database update failed."
    ]);

}

?>