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
    $queries = [
        "DELETE ci FROM cart_items ci INNER JOIN cart c ON c.cart_id = ci.cart_id WHERE c.user_id = ?",
        "DELETE FROM cart WHERE user_id = ?",
        "DELETE oi FROM order_items oi INNER JOIN orders o ON o.order_id = oi.order_id WHERE o.user_id = ?",
        "DELETE FROM orders WHERE user_id = ?",
        "DELETE FROM users WHERE id = ?"
    ];

    foreach ($queries as $sql) {
        $statement = mysqli_prepare($conn, $sql);
        if (!$statement) {
            throw new Exception("Could not prepare deletion.");
        }

        mysqli_stmt_bind_param($statement, "i", $userId);
        if (!mysqli_stmt_execute($statement)) {
            throw new Exception("Could not delete user data.");
        }

        if ($sql === "DELETE FROM users WHERE id = ?" && mysqli_stmt_affected_rows($statement) !== 1) {
            throw new Exception("User was not found.");
        }

        mysqli_stmt_close($statement);
    }

    mysqli_commit($conn);
    echo json_encode(["success" => true]);
} catch (Throwable $error) {
    mysqli_rollback($conn);
    http_response_code(500);
    echo json_encode(["success" => false, "message" => $error->getMessage()]);
}

?>
