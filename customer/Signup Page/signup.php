<?php
/* ============================================================
   SIGNUP — process registration
   Collects: email, username, phone, password (as specified)
   - Validates required fields + matching passwords
   - Checks email/username aren't already taken
   - Hashes the password
   - Inserts the row, starts the session, redirects to landing page
   ============================================================ */

require '../../config/db_connect.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: /tcgzone/customer/Signup Page/signup.html");
    exit;
}

$email            = trim($_POST['email'] ?? '');
$username         = trim($_POST['username'] ?? '');
$phone            = trim($_POST['phone'] ?? '');
$password         = $_POST['password'] ?? '';
$confirmPassword  = $_POST['confirmPassword'] ?? '';


$error = [];

// ---- Validation ---------------------------------------------------------
if ($password !== $confirmPassword) {
    $errors['match'] = 1;
}

if (strlen($password) < 8) {
    $errors['password'] = 1;
}


// ---- Check for existing email/username -----------------------------------
// Check email
$checkEmail = $conn->prepare("SELECT id FROM users WHERE email = ?");
$checkEmail->bind_param("s", $email);
$checkEmail->execute();

if ($checkEmail->get_result()->num_rows > 0) {
    $errors['email'] = 1;
}

// Check username
$checkUsername = $conn->prepare("SELECT id FROM users WHERE username = ?");
$checkUsername->bind_param("s", $username);
$checkUsername->execute();

if ($checkUsername->get_result()->num_rows > 0) {
    $errors['username'] = 1;
}


if (!empty($errors)) {
    $errors['old_email']    = $email;
    $errors['old_username'] = $username;
    $errors['old_phone']    = $phone;

    header("Location: signup.html?" . http_build_query($errors));
    exit;
}

// ---- Insert the new user --------------------------------------------------
$passwordHash = password_hash($password, PASSWORD_DEFAULT);

$insert = $conn->prepare("INSERT INTO users (username, email, password_hash, phone) VALUES (?, ?, ?, ?)");
$insert->bind_param("ssss", $username, $email, $passwordHash, $phone);
$insert->execute();

// ---- Log the new user in immediately --------------------------------------
$_SESSION['user_id']  = $insert->insert_id;
$_SESSION['username'] = $username;

header("Location: /tcgzone/customer/Landing Page/index.php");
exit;
