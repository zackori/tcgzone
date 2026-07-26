<?php

header("Content-Type: application/json");

include "../../../config/db_connect.php";

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    http_response_code(405);
    echo json_encode(["success" => false, "message" => "POST requests only."]);
    exit;
}

$data = json_decode(file_get_contents("php://input"), true);
$userId = isset($data["user_id"]) ? (int) $data["user_id"] : 0;

if ($userId <= 0) {
    http_response_code(422);
    echo json_encode(["success" => false, "message" => "A valid user is required."]);
    exit;
}

mysqli_begin_transaction($conn);

try {
    // First, ensure the is_archived columns exist (run migration if needed)
    $checkColumn = mysqli_query($conn, "SHOW COLUMNS FROM users LIKE 'is_archived'");
    if (mysqli_num_rows($checkColumn) === 0) {
        // Add the columns if they don't exist
        mysqli_query($conn, "ALTER TABLE users ADD COLUMN is_archived TINYINT(1) NOT NULL DEFAULT 0 AFTER created_at");
        mysqli_query($conn, "ALTER TABLE users ADD COLUMN archived_at DATETIME NULL AFTER is_archived");
    }

    // Archive the user instead of deleting - keeps all records for financial tracking
    $sql = "UPDATE users SET is_archived = 1, archived_at = NOW() WHERE id = ?";
    $statement = mysqli_prepare($conn, $sql);
    if (!$statement) {
        throw new Exception("Could not prepare archive query.");
    }

    mysqli_stmt_bind_param($statement, "i", $userId);
    if (!mysqli_stmt_execute($statement)) {
        throw new Exception("Could not archive user.");
    }

    if (mysqli_stmt_affected_rows($statement) !== 1) {
        throw new Exception("User was not found.");
    }

    mysqli_stmt_close($statement);

    // Clear their cart (but keep orders for financial records)
    $cartQueries = [
        "DELETE ci FROM cart_items ci INNER JOIN cart c ON c.cart_id = ci.cart_id WHERE c.user_id = ?",
        "DELETE FROM cart WHERE user_id = ?"
    ];

    foreach ($cartQueries as $cartSql) {
        $cartStatement = mysqli_prepare($conn, $cartSql);
        if (!$cartStatement) {
            throw new Exception("Could not prepare cart deletion.");
        }

        mysqli_stmt_bind_param($cartStatement, "i", $userId);
        if (!mysqli_stmt_execute($cartStatement)) {
            throw new Exception("Could not clear user cart.");
        }

        mysqli_stmt_close($cartStatement);
    }

    mysqli_commit($conn);

    // Create a marker file to invalidate sessions for this user
    $sessionDir = sys_get_temp_dir() . '/tcgzone_invalidated_sessions';
    if (!is_dir($sessionDir)) {
        mkdir($sessionDir, 0777, true);
    }
    file_put_contents($sessionDir . '/' . $userId . '.invalidated', time());

    echo json_encode(["success" => true]);
} catch (Throwable $error) {
    mysqli_rollback($conn);
    http_response_code(500);
    echo json_encode(["success" => false, "message" => $error->getMessage()]);
}

?>