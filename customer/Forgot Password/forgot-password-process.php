<?php
header('Content-Type: application/json');

require '../../config/db_connect.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method.']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);
$action = $input['action'] ?? '';
$email = trim($input['email'] ?? '');

if ($action === 'check_email') {
    if (empty($email)) {
        echo json_encode(['status' => 'error', 'message' => 'Email is required.']);
        exit;
    }

    $stmt = $conn->prepare('SELECT id FROM users WHERE email = ?');
    $stmt->bind_param('s', $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if (!$user) {
        echo json_encode(['status' => 'error', 'message' => 'No account was found with that email.']);
        exit;
    }

    echo json_encode(['status' => 'ok']);
    exit;
}

if ($action === 'reset_password') {
    $password = $input['password'] ?? '';
    if (empty($email) || empty($password)) {
        echo json_encode(['status' => 'error', 'message' => 'Email and password are required.']);
        exit;
    }

    if (strlen($password) < 8) {
        echo json_encode(['status' => 'error', 'message' => 'Password must be at least 8 characters long.']);
        exit;
    }

    $stmt = $conn->prepare('SELECT id FROM users WHERE email = ?');
    $stmt->bind_param('s', $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if (!$user) {
        echo json_encode(['status' => 'error', 'message' => 'No account was found with that email.']);
        exit;
    }

    $passwordHash = password_hash($password, PASSWORD_DEFAULT);
    $update = $conn->prepare('UPDATE users SET password_hash = ? WHERE id = ?');
    $update->bind_param('si', $passwordHash, $user['id']);
    $updated = $update->execute();

    if (!$updated) {
        echo json_encode(['status' => 'error', 'message' => 'Unable to update your password at this time.']);
        exit;
    }

    echo json_encode(['status' => 'ok']);
    exit;
}

echo json_encode(['status' => 'error', 'message' => 'Unknown action.']);
header("Location: /tcgzone/customer/Login Page/login.html");
exit;
