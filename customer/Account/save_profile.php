<?php
/* ============================================================
   SAVE PROFILE — updates email, phone, address, dob, gender
   for the logged-in user
   ============================================================ */

require '../../config/db_connect.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: /tcgzone/customer/Login Page/login.html");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: /tcgzone/customer/Landing Page/index.php");
    exit;
}

$userId = $_SESSION['user_id'];

$email            = $_POST['email'] ?? '';
$phone            = $_POST['phone'] ?? '';
$dob              = $_POST['dob'] ?? null;
$first_name       = $_POST['first_name'] ?? '';
$last_name        = $_POST['last_name'] ?? '';
$address_details  = $_POST['address_details'] ?? '';
$address_city     = $_POST['address_city'] ?? '';
$address_province = $_POST['address_province'] ?? '';
$address_zip      = $_POST['address_zip'] ?? '';
$gender           = $_POST['gender'] ?? 'Male';

if ($dob === '') {
    $dob = null;
}

$stmt = $conn->prepare("
    UPDATE users
    SET email = ?, phone = ?, dob = ?,
        first_name = ?, last_name = ?,
        address_details = ?, address_city = ?, address_province = ?, address_zip = ?,
        gender = ?
    WHERE id = ?
");

$stmt->bind_param(
    "ssssssssssi",
    $email, $phone, $dob,
    $first_name, $last_name,
    $address_details, $address_city, $address_province, $address_zip,
    $gender, $userId
);

$stmt->execute();

header("Location: /tcgzone/customer/Landing Page/index.php");
exit;
