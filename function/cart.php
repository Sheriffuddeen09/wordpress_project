<?php

require_once '../database/function.php';
header("Content-Type: application/json");

error_reporting(E_ALL);
ini_set('display_errors', 1);

global $conn; // Ensure $conn is available
if (!$conn) {
    echo json_encode(["success" => false, "error" => "Database connection failed."]);
    exit;
}


// Get user_id from request (GET or POST)
$data = json_decode(file_get_contents("php://input"), true);

$user_id = isset($_GET['user_id']) ? intval($_GET['user_id']) : (isset($data['user_id']) ? intval($data['user_id']) : null);

// Check if user_id is missing
if (!$user_id) {
    echo json_encode(["success" => false, "error" => "User ID is missing"]);
    exit;
}

try {

    // SQL Query to fetch cart items
    $query = "SELECT 
                c.id AS cart_id, 
                p.id AS product_id, 
                p.title, 
                p.image, 
                p.maintenance, 
                p.location,
                p.price, 
                c.quantity, 
                c.total_price 
              FROM cart c 
              JOIN products p ON c.product_id = p.id 
              WHERE c.user_id = ?";

    $stmt = $conn->prepare($query);
    $stmt->bind_param('i', $user_id);
    $stmt->execute();

    $result = $stmt->get_result();

    $cartItems = $result->fetch_all(MYSQLI_ASSOC);

    echo json_encode(["success" => true, "cart" => $cartItems]);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(["error" => "Database error: " . $e->getMessage()]);
}
?>
