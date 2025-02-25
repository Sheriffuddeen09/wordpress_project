<?php

header('Content-Type: application/json'); // Ensure JSON response

// Enable error logging (for debugging)
ini_set('display_errors', 1); // Show errors
ini_set('log_errors', 1); // Log errors
error_reporting(E_ALL);

require_once '../database/function.php';


// Handle preflight (OPTIONS) request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

$user_id = $_GET['user_id'] ?? null;

if (!$user_id) {
    echo json_encode(["success" => false, "error" => "User ID is required"]);
    exit;
}

try {
    global $conn; // ✅ Fixed missing semicolon

    // ✅ Use PDO prepared statements to prevent SQL injection
    $query = "SELECT message, type, image, status, created_at 
              FROM notifications 
              WHERE user_id = ? OR user_id IS NULL 
              ORDER BY created_at DESC";

    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();

    $result = $stmt->get_result();

    $notifications = $result->fetch_all(MYSQLI_ASSOC);

    echo json_encode(["success" => true, "notifications" => $notifications]);

} catch (Exception $e) {
    echo json_encode(["success" => false, "error" => $e->getMessage()]);
}
?>
