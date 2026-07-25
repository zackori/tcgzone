<?php
session_start();
header('Content-Type: application/json; charset=utf-8');

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Not authenticated.']);
    exit;
}

require_once __DIR__ . '/../../config/db_connect.php';

$userId = (int) $_SESSION['user_id'];

$sql = "
    SELECT
        request_id,
        card_name,
        set_name,
        category,
        product_type,
        rarity,
        `condition`,
        selling_price,
        stock_quantity AS quantity,
        status,
        notes,
        created_at
    FROM sell_requests
    WHERE user_id = ?
    ORDER BY created_at DESC, request_id DESC
";

$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, 'i', $userId);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

$history = [];
while ($row = mysqli_fetch_assoc($result)) {
    $history[] = [
        'request_id' => (int) $row['request_id'],
        'card_name' => $row['card_name'],
        'set_name' => $row['set_name'],
        'category' => $row['category'],
        'product_type' => $row['product_type'],
        'rarity' => $row['rarity'],
        'condition' => $row['condition'],
        'selling_price' => (float) $row['selling_price'],
        'quantity' => (int) $row['quantity'],
        'status' => $row['status'],
        'notes' => $row['notes'],
        'created_at' => $row['created_at'],
    ];
}

mysqli_stmt_close($stmt);
mysqli_close($conn);

echo json_encode(['success' => true, 'requests' => $history]);
