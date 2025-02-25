<?php
header('Content-Type: application/json'); // Ensure JSON response

// Enable error logging (for debugging)
ini_set('display_errors', 1); // Show errors
ini_set('log_errors', 1); // Log errors
error_reporting(E_ALL);

require_once '../database/function.php';

// Read raw input data
$json = file_get_contents('php://input');

// Log received data for debugging
file_put_contents("debug_log.txt", "Received JSON: " . $json . PHP_EOL, FILE_APPEND);

$data = json_decode($json, true);

if (!$json) {
    echo json_encode(["error" => "No JSON data received"]);
    exit;
}

if (!$data) {
    echo json_encode(["error" => "Invalid JSON received", "received" => $json]);
    exit;
}

// Debugging: Check received data
error_log("Received Data: " . print_r($data, true));

// Check if required fields exist
if (!isset($data['user_id'], $data['product_id'], $data['quantity'])) {
    echo json_encode(["error" => "Missing required fields"]);
    exit;
}

// Assign variables safely
$user_id = intval($data['user_id']);
$product_id = intval($data['product_id']);
$quantity = intval($data['quantity']);
$total_price = isset($data['total_price']) ? floatval($data['total_price']) : 0;

// Ensure database connection is available
if (!isset($conn) || !$conn) {
    echo json_encode(["error" => "Database connection failed"]);
    exit;
}

// Prepare SQL statement
$stmt = $conn->prepare("INSERT INTO cart (user_id, product_id, quantity, total_price) VALUES (?, ?, ?, ?)");
if (!$stmt) {
    echo json_encode(["error" => "SQL Prepare failed: " . $conn->error]);
    exit;
}

// Bind and execute
$stmt->bind_param("iiid", $user_id, $product_id, $quantity, $total_price);
if ($stmt->execute()) {
    echo json_encode(["success" => true, "message" => "Product added to cart!"]);
} else {
    echo json_encode(["error" => "Database error: " . $stmt->error]);
}

// Close statement
$stmt->close();
?>
