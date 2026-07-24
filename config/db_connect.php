<?php
/* ============================================================
   DATABASE CONNECTION
   Default XAMPP settings: user "root", no password.
   If you set a MySQL password in XAMPP, update $db_password below.
   ============================================================ */

$servername  = "localhost";
$db_username = "root";
$db_password = "";
$dbname      = "tcgzone";

$conn = new mysqli($servername, $db_username, $db_password, $dbname);

if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}
