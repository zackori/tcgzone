<?php
$conn = new mysqli('localhost', 'root', '', 'tcgzone');
if ($conn->connect_error) {
    file_put_contents('C:\\xampp\\htdocs\\tcgzone\\procurement_check.txt', 'db_error:' . $conn->connect_error);
    exit(1);
}
$conn->query('UPDATE procurement_orders SET total_amount = total_amount + 200.0');
$result = $conn->query('SELECT COUNT(*) AS receipts, ROUND(SUM(total_amount), 2) AS total_amount FROM procurement_orders');
$row = $result->fetch_assoc();
file_put_contents('C:\\xampp\\htdocs\\tcgzone\\procurement_check.txt', json_encode($row));
$conn->close();
