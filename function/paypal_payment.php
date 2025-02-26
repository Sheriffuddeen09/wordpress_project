<?php
require_once '../database/function.php';

$order_id = $_GET['order_id'] ?? null;

if (!$order_id) {
    die("Invalid order ID.");
}

// Simulating PayPal Payment Success
$sql = "UPDATE orders SET status = 'Paid' WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $order_id);
$stmt->execute();

// Redirect to order tracking
header("Location: /order-tracking?order_id=$order_id");
exit;
?>
