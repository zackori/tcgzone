<?php
/**
 * Database Migration - Add is_archived columns for user archiving
 * Run this script once to add the necessary columns to the users table
 */

require '../../config/db_connect.php';

try {
    // Check if is_archived column already exists
    $result = mysqli_query($conn, "SHOW COLUMNS FROM users LIKE 'is_archived'");

    if (mysqli_num_rows($result) === 0) {
        // Column doesn't exist, add it
        $alterQueries = [
            "ALTER TABLE users ADD COLUMN is_archived TINYINT(1) NOT NULL DEFAULT 0 AFTER created_at",
            "ALTER TABLE users ADD COLUMN archived_at DATETIME NULL AFTER is_archived"
        ];

        foreach ($alterQueries as $query) {
            if (!mysqli_query($conn, $query)) {
                throw new Exception("Error executing query: " . mysqli_error($conn));
            }
        }

        echo json_encode([
            "success" => true,
            "message" => "Database migration completed successfully. Users table now has is_archived and archived_at columns."
        ]);
    } else {
        echo json_encode([
            "success" => true,
            "message" => "Columns already exist. No migration needed."
        ]);
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        "success" => false,
        "message" => "Migration failed: " . $e->getMessage()
    ]);
}
?>