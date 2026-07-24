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

$newPassword      = $_POST['new_password'] ?? '';
$confirmPassword  = $_POST['confirm_password'] ?? '';

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

// 2. 🌟 PROCESS NEW PASSWORD IF USER SENT ONE
if (!empty($newPassword)) {
    // Only update if passwords match and satisfy length requirements
    if ($newPassword === $confirmPassword && strlen($newPassword) >= 8) {
        $passwordHash = password_hash($newPassword, PASSWORD_DEFAULT);
        
        $pwdStmt = $conn->prepare("UPDATE users SET password_hash = ? WHERE id = ?");
        $pwdStmt->bind_param("si", $passwordHash, $userId);
        $pwdStmt->execute();
    } else {
        // Optional error fallback query string (e.g. passwords mismatched/too short)
        header("Location: account.php?error=password_invalid");
        exit;
    }
}


header("Location: /tcgzone/customer/Landing Page/index.php");
exit;
