<?php
require_once '../database/function.php';

header("Content-Type: application/json");

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Read and decode JSON input
$input = json_decode(file_get_contents("php://input"), true);

if (!$input) {
    echo json_encode(["success" => false, "message" => "Invalid JSON input"]);
    exit;
}

// 🛠️ FIXED: Corrected Error Message
if (!isset($input['category_id']) || empty($input['category_id'])) {
    echo json_encode(["success" => false, "message" => "Category ID is required"]);
    exit;
}

$category_id = intval($input['category_id']);
$user_id = intval($input['user_id']);
$review_text = trim($input['review_text']);
$rating = intval($input['rating']);

if (!$user_id || !$rating || empty($review_text)) {
    echo json_encode(["success" => false, "message" => "All fields are required"]);
    exit;
}

global $conn;

try {
    // Insert review into database
    $query = "INSERT INTO reviews (user_id, category_id, rating, review_text) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("iiis", $user_id, $category_id, $rating, $review_text);
    
    if ($stmt->execute()) {
        echo json_encode(["success" => true, "message" => "Review added successfully"]);
    } else {
        echo json_encode(["success" => false, "message" => "Failed to add review"]);
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(["error" => "Database error: " . $e->getMessage()]);
}
?>
