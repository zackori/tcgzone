<?php
/* ============================================================
   LOGIN — process sign in
   - Looks up the user by email, verifies the hashed password
   - Starts the session, redirects to landing page
   ============================================================ */

require '../../config/db_connect.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: /tcgzone/customer/Login Page/login.html");
    exit;
}

$email    = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';

if ($email === '' || $password === '') {
    die("Please fill in all fields. <a href='/tcgzone/customer/Login Page/login.html'>Go back</a>");
}

$stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if (!$user || !password_verify($password, $user['password_hash'])) {
    die("Incorrect email or password. <a href='/tcgzone/customer/Login Page/login.html'>Go back</a>");
}

$_SESSION['user_id']  = $user['id'];
$_SESSION['username'] = $user['username'];

header("Location: /tcgzone/customer/Landing Page/index.php");
exit;
