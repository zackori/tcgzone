<?php

header("Content-Type: application/json");

session_start();

// Check if user is logged in
if (!isset($_SESSION["user_id"])) {
    http_response_code(401);
    echo json_encode(["valid" => false, "message" => "Not logged in"]);
    exit;
}

$userId = (int) $_SESSION["user_id"];

// Check if the user's session has been invalidated (account deleted/archived by admin)
$sessionDir = sys_get_temp_dir() . '/tcgzone_invalidated_sessions';
if (is_dir($sessionDir) && file_exists($sessionDir . '/' . $userId . '.invalidated')) {
    // This user's account was archived, log them out
    session_destroy();
    http_response_code(401);
    echo json_encode(["valid" => false, "message" => "Account has been archived"]);
    exit;
}

include "../../config/db_connect.php";

// Verify the user still exists and is not archived
$sql = "SELECT id FROM users WHERE id = ? AND is_archived = 0";
$statement = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($statement, "i", $userId);
mysqli_stmt_execute($statement);
$result = mysqli_stmt_get_result($statement);
mysqli_stmt_close($statement);

if (mysqli_num_rows($result) === 0) {
    // User no longer exists or is archived, log them out
    session_destroy();
    http_response_code(401);
    echo json_encode(["valid" => false, "message" => "Account has been archived"]);
    exit;
}

echo json_encode(["valid" => true]);

?>