<?php
$conn = new mysqli("localhost", "root", "", "tcgzone");
if ($conn->connect_error) {
    fwrite(STDERR, "DB connection failed: " . $conn->connect_error . PHP_EOL);
    exit(1);
}
$result = $conn->query("UPDATE procurement_orders SET total_amount = total_amount + 200.0");
if ($result === false) {
    fwrite(STDERR, "Update failed: " . $conn->error . PHP_EOL);
    $conn->close();
    exit(1);
}
echo "updated_rows=" . $conn->affected_rows . PHP_EOL;
$conn->close();
