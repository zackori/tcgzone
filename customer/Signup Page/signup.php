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

// ---- Validation ---------------------------------------------------------
if ($email === '' || $username === '' || $password === '' || $confirmPassword === '') {
    die("Please complete all fields. <a href='/tcgzone/customer/Signup Page/signup.html'>Go back</a>");
}

if ($password !== $confirmPassword) {
    die("Passwords do not match. <a href='/tcgzone/customer/Signup Page/signup.html'>Go back</a>");
}

if (strlen($password) < 8) {
    die("Password must be at least 8 characters. <a href='/tcgzone/customer/Signup Page/signup.html'>Go back</a>");
}

// ---- Check for existing email/username -----------------------------------
$check = $conn->prepare("SELECT id FROM users WHERE email = ? OR username = ?");
$check->bind_param("ss", $email, $username);
$check->execute();
$existing = $check->get_result();

if ($existing->num_rows > 0) {
    die("An account with that email or username already exists. <a href='/tcgzone/customer/Signup Page/signup.html'>Go back</a>");
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
