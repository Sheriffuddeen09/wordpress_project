<?php
header('Content-Type: application/json');

$data = json_decode(file_get_contents("php://input"), true);
$order_id = $data['order_id'] ?? null;
$payment_method = $data['payment_method'] ?? null;

if (!$order_id) {
    echo json_encode(["success" => false, "error" => "Order ID is missing"]);
    exit;
}

if ($payment_method === "PayPal") {
    $paypal_url = "https://www.paypal.com/checkout";  // Use actual PayPal API
    echo json_encode(["success" => true, "redirect_url" => $paypal_url]);
} elseif ($payment_method === "Stripe") {
    $stripe_url = "http://localhost/stripe_checkout";
    echo json_encode(["success" => true, "redirect_url" => $stripe_url]);
} else {
    echo json_encode(["success" => true, "redirect_url" => "/order-tracking"]);
}
?>
