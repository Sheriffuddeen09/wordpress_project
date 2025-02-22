<?php

require_once '../database/function.php';

header("Content-Type: application/json");

global $conn;
// Handle preflight (OPTIONS) request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
}
$data = json_decode(file_get_contents("php://input"), true);

if (!$data) {
    echo json_encode(["success" => false, "message" => "Invalid JSON data"]);
    exit;
}

$user_id = isset($data['user_id']) ? intval($data['user_id']) : null;
$total_price = isset($data['total_price']) ? $data['total_price'] : null;
$payment_method = isset($data['payment_method']) ? $data['payment_method'] : null;
$items = $data['items'] ?? [];
$shipment = $data['shipment'] ?? [];

if (!$user_id) {
    echo json_encode(["success" => false, "message" => "Missing required fields"]);
    exit;
}

try {
    global $conn;

    // Generate unique transaction ID
    $transaction_id = uniqid("TXN-");

    // Insert order into database
    $queryorder = "INSERT INTO orders (user_id, total_price, payment_method, transaction_id) 
                   VALUES (:user_id, :total_price, :payment_method, :transaction_id)";
    $stmt = $conn->prepare($queryorder);
    $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
    $stmt->bindParam(':total_price', $total_price, PDO::PARAM_STR);
    $stmt->bindParam(':payment_method', $payment_method, PDO::PARAM_STR);
    $stmt->bindParam(':transaction_id', $transaction_id, PDO::PARAM_STR);
    $stmt->execute();
    $order_id = $conn->lastInsertId(); // Get last inserted order ID

    // Insert items into order_items table
    foreach ($items as $item) {
        $queryitem = "INSERT INTO order_items (order_id, image, title, price, color, size, quantity, pick_up_date, pick_up_time, drop_off_date, drop_off_time) 
                      VALUES (:order_id, :image, :title, :price, :color, :size, :quantity, :pick_up_date, :pick_up_time, :drop_off_date, :drop_off_time)";
        $stmt = $conn->prepare($queryitem);
        $stmt->bindParam(':order_id', $order_id, PDO::PARAM_INT);
        $stmt->bindParam(':image', $item['image'], PDO::PARAM_STR);
        $stmt->bindParam(':title', $item['title'], PDO::PARAM_STR);
        $stmt->bindParam(':price', $item['price'], PDO::PARAM_STR);
        $stmt->bindParam(':color', $item['color'], PDO::PARAM_STR);
        $stmt->bindParam(':size', $item['size'], PDO::PARAM_STR);
        $stmt->bindParam(':quantity', $item['quantity'], PDO::PARAM_INT);
        $stmt->bindParam(':pick_up_date', $item['pick_up_date'], PDO::PARAM_STR);
        $stmt->bindParam(':pick_up_time', $item['pick_up_time'], PDO::PARAM_STR);
        $stmt->bindParam(':drop_off_date', $item['drop_off_date'], PDO::PARAM_STR);
        $stmt->bindParam(':drop_off_time', $item['drop_off_time'], PDO::PARAM_STR);
        $stmt->execute();
    }

    // Insert shipment details
    $tracking_number = "TRK" . rand(100000, 999999);
    $queryshipment = "INSERT INTO shipment (order_id, recipient_firstname, recipient_lastname, recipient_phone, address, city, state, zip_code, country, tracking_number) 
                      VALUES (:order_id, :recipient_firstname, :recipient_lastname, :recipient_phone, :address, :city, :state, :zip_code, :country, :tracking_number)";
    $stmt = $conn->prepare($queryshipment);
    $stmt->bindParam(':order_id', $order_id, PDO::PARAM_INT);
    $stmt->bindParam(':recipient_firstname', $shipment['recipient_firstname'], PDO::PARAM_STR);
    $stmt->bindParam(':recipient_lastname', $shipment['recipient_lastname'], PDO::PARAM_STR);
    $stmt->bindParam(':recipient_phone', $shipment['recipient_phone'], PDO::PARAM_STR);
    $stmt->bindParam(':address', $shipment['address'], PDO::PARAM_STR);
    $stmt->bindParam(':city', $shipment['city'], PDO::PARAM_STR);
    $stmt->bindParam(':state', $shipment['state'], PDO::PARAM_STR);
    $stmt->bindParam(':zip_code', $shipment['zip_code'], PDO::PARAM_STR);
    $stmt->bindParam(':country', $shipment['country'], PDO::PARAM_STR);
    $stmt->bindParam(':tracking_number', $tracking_number, PDO::PARAM_STR);
    $stmt->execute();

    echo json_encode([
        "success" => true,
        "order_id" => $order_id,
        "transaction_id" => $transaction_id,
        "tracking_number" => $tracking_number
    ]);
} catch (PDOException $e) {
    echo json_encode(["success" => false, "message" => "Database error: " . $e->getMessage()]);
}

?>
