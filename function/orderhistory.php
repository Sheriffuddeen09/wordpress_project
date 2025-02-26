<?php

require_once '../database/function.php';

header("Content-Type: application/json");

// ✅ **Handle preflight (OPTIONS) request immediately**
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

try {
    global $conn; // ✅ FIXED: Added semicolon

    // Fetch orders from database
    $sql = "SELECT id, tracking_number, status, payment_method, total_price FROM orders";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $orders = $result->fetch_all(MYSQLI_ASSOC); // ✅ FIXED: Correct PDO fetching method

    if (count($orders) > 0) {
        echo json_encode(["success" => true, "orders" => $orders]);
    } else {
        echo json_encode(["success" => false, "message" => "No orders found"]);
    }
} catch (PDOException $e) {
    echo json_encode(["success" => false, "message" => "Database error: " . $e->getMessage()]);
}
?>
