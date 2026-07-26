<?php

header("Content-Type: application/json");

include "../../../config/db_connect.php";

// Simple query to get all users
$sql = "SELECT id, username, first_name, last_name, email, phone, address_details, address_city, address_province, address_zip FROM users ORDER BY id DESC";

$result = mysqli_query($conn, $sql);

if (!$result) {
    http_response_code(500);
    echo json_encode(["error" => "Could not load users: " . mysqli_error($conn)]);
    exit;
}

$users = [];

while ($row = mysqli_fetch_assoc($result)) {
    // Format the name
    $name = trim(($row['first_name'] ?? '') . ' ' . ($row['last_name'] ?? ''));

    // Format the address
    $addressParts = array_filter([
        $row['address_details'],
        $row['address_city'],
        $row['address_province'],
        $row['address_zip']
    ]);
    $address = implode(', ', $addressParts);

    // Get pending order status
    $userId = $row['id'];
    $pendingSql = "SELECT COUNT(*) as count FROM orders WHERE user_id = ? AND order_status = 'Pending' LIMIT 1";
    $pendingStmt = mysqli_prepare($conn, $pendingSql);

    if ($pendingStmt) {
        mysqli_stmt_bind_param($pendingStmt, "i", $userId);
        mysqli_stmt_execute($pendingStmt);
        $pendingResult = mysqli_stmt_get_result($pendingStmt);
        $pendingRow = mysqli_fetch_assoc($pendingResult);
        $hasPending = ($pendingRow['count'] > 0) ? 1 : 0;
        mysqli_stmt_close($pendingStmt);
    } else {
        $hasPending = 0;
    }

    $users[] = [
        'id' => $row['id'],
        'username' => $row['username'],
        'name' => $name,
        'email' => $row['email'],
        'phone' => $row['phone'],
        'address' => $address,
        'has_pending_order' => $hasPending
    ];
}

echo json_encode($users);

?>