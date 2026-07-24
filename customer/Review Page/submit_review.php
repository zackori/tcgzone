<?php
/* ============================================================
SUBMIT REVIEW
- Called from review.js after client-side validation passes
- Requires login — every review is tied to a user_id.
    review.js already redirects to login before this is ever
    called, but this checks again server-side too, since the
    JS check alone could be bypassed.
- Responds with a tiny JSON success flag (same reason as
    update_billing.php: the JS does fetch().then(r => r.json()))
============================================================ */

require '../../config/db_connect.php';
session_start();

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Invalid request method']);
    exit;
}

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'error' => 'You must be logged in to leave a review']);
    exit;
}

$name       = trim($_POST['name'] ?? '') ?: 'Anonymous';
$rating     = (int)($_POST['rating'] ?? 0);
$reviewText = trim($_POST['review'] ?? '');
$userId     = $_SESSION['user_id'];

if ($rating < 1 || $rating > 5) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Rating must be between 1 and 5']);
    exit;
}

if ($reviewText === '') {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Review text is required']);
    exit;
}

$stmt = $conn->prepare("
    INSERT INTO reviews (user_id, name, rating, review_text)
    VALUES (?, ?, ?, ?)
");
$stmt->bind_param("isis", $userId, $name, $rating, $reviewText);

$success = $stmt->execute();

echo json_encode(['success' => $success]);
