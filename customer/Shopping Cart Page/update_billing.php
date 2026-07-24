<?php
/* ============================================================
   UPDATE BILLING — called from shopping-cart.js when "Place
   Order" is clicked. Same job as save_profile.php on the
   Account page: takes whatever's in the form right now and
   writes it to the logged-in user's row.

   Responds with a tiny JSON success flag only because the JS
   uses fetch().then(r => r.json()) — you don't need to write
   any JSON yourself, this is the only place it's used.
   ============================================================ */

require '../../config/db_connect.php';
session_start();

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'error' => 'Not logged in']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Invalid request method']);
    exit;
}

$userId = $_SESSION['user_id'];

$first_name       = trim($_POST['firstName'] ?? '');
$last_name        = trim($_POST['lastName'] ?? '');
$email            = trim($_POST['email'] ?? '');
$phone            = trim($_POST['phone'] ?? '');
$address_details  = trim($_POST['houseNumber'] ?? '');
$address_city     = trim($_POST['city'] ?? '');
$address_province = trim($_POST['province'] ?? '');
$address_zip      = trim($_POST['zipCode'] ?? '');

$stmt = $conn->prepare("
    UPDATE users
    SET first_name = ?, last_name = ?, email = ?, phone = ?,
        address_details = ?, address_city = ?, address_province = ?, address_zip = ?
    WHERE id = ?
");

$stmt->bind_param(
    "ssssssssi",
    $first_name, $last_name, $email, $phone,
    $address_details, $address_city, $address_province, $address_zip,
    $userId
);

$success = $stmt->execute();

echo json_encode(['success' => $success]);
