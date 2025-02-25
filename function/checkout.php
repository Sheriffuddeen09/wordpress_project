<?php

require_once '../database/function.php';

header("Content-Type: application/json");

$data = json_decode(file_get_contents("php://input"), true);

if (!isset($data['user_id']) || !isset($data['items']) || count($data['items']) === 0) {
    echo json_encode(["success" => false, "message" => "Cart is empty or user ID missing."]);
    exit;
}

$user_id = $data['user_id'];
$payment_method = $data['payment_method'];
$total_price = $data['total_price'];
$shipment = $data['shipment'];
$items = $data['items'];

// Insert order into the orders table
$sql = "INSERT INTO orders (user_id, total_price, payment_method, status) VALUES (?, ?, ?, 'Pending')";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ids", $user_id, $total_price, $payment_method);
$stmt->execute();
$order_id = $stmt->insert_id; // Get the last inserted order ID

// Insert shipment details
$shipmentSql = "INSERT INTO shipment (order_id, recipient_firstname, recipient_lastname, recipient_phone, address, city, state, zip_code, country) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
$shipmentStmt = $conn->prepare($shipmentSql);
$shipmentStmt->bind_param("issssssss", $order_id, 
    $shipment['recipient_firstname'], 
    $shipment['recipient_lastname'], 
    $shipment['recipient_phone'], 
    $shipment['address'], 
    $shipment['city'], 
    $shipment['state'], 
    $shipment['zip_code'], 
    $shipment['country']
);
$shipmentStmt->execute();

// Insert items into order_items table
foreach ($items as $item) {
    $product_id = $item['product_id'];
    $image = $item['image'];
    $title = $item['title'];
    $price = $item['price'];
    $color = $item['color'];
    $size = $item['size'];
    $quantity = $item['quantity'];
    $pick_up_date = $item['pick_up_date'];
    $pick_up_time = $item['pick_up_time'];
    $drop_off_date = $item['drop_off_date'];
    $drop_off_time = $item['drop_off_time'];

    $itemSql = "INSERT INTO order_items (order_id, product_id, image, title, price, color, size, quantity, pick_up_date, pick_up_time, drop_off_date, drop_off_time) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $itemStmt = $conn->prepare($itemSql);
    $itemStmt->bind_param("iissdssissss", $order_id, $product_id, $image, $title, $price, $color, $size, $quantity, $pick_up_date, $pick_up_time, $drop_off_date, $drop_off_time);
    $itemStmt->execute();
}

// Send success response
echo json_encode(["success" => true, "message" => "Order placed successfully!"]);
exit;
?>
