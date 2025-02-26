<?php

require_once '../database/function.php';

header("Content-Type: application/json");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

$order_id = $_GET['order_id'] ?? '';

if (!$order_id) {
    echo json_encode(["success" => false, "message" => "Order ID is required"]);
    exit;
}

try {
    global $conn;

    // Prepare SQL Query
    $stmt = $conn->prepare("SELECT id, tracking_number, status, payment_method, total_price, address, city, state, zip_code, country FROM orders WHERE id = ?");
    $stmt->bind_param("i", $order_id);
    $stmt->execute();
    $result = $stmt->get_result();

    // Fetch Order Data
    $order = $result->fetch_assoc();
    
    if (!$order) {
        echo json_encode(["success" => false, "message" => "Order not found"]);
        exit;
    }

    // Fetch Order Items
    $stmt = $conn->prepare("SELECT product_id, title, size, color, price, quantity, image FROM order_items WHERE order_id = ?");
    $stmt->bind_param("i", $order_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $items = [];
    while ($row = $result->fetch_assoc()) {
        $items[] = $row;
    }

    $order['items'] = $items;

    // Send JSON response
    echo json_encode(["success" => true, "order" => $order]);

} catch (Exception $e) {
    echo json_encode(["success" => false, "message" => "Server error: " . $e->getMessage()]);
}

?>
