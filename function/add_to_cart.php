<?php

require_once 'database.php'; // Ensure database connection is included

$allowedOrigins = ['http://localhost:3000', 'http://localhost:3001', 'http://another-allowed-origin.com'];

if (isset($_SERVER['HTTP_ORIGIN']) && in_array($_SERVER['HTTP_ORIGIN'], $allowedOrigins)) {
    header("Access-Control-Allow-Origin: " . $_SERVER['HTTP_ORIGIN']);
    header("Access-Control-Allow-Credentials: true");
    header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
    header("Access-Control-Allow-Headers: Content-Type, Authorization");
    header("Access-Control-Max-Age: 86400"); 
}

// **Handle preflight requests early**
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);

$quantity = $data['quantity'] ?? '';
$product_id = $data['product_id'];
$user_id = $data['user_id'];

try {
    global $pdo;

    // Fetch product details from the database
    $query = "SELECT title, price, security_amount, image FROM products WHERE id = :product_id";
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':product_id', $product_id, PDO::PARAM_INT);
    $stmt->execute();
    $product = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$product) {
        echo json_encode(["error" => "Product not found in database"]);
        exit;
    }

    // Calculate total price including security amount
    $price = floatval($product['price']);
    $security_amount = floatval($product['security_amount']);
    $total_price = ($price * $quantity) + $security_amount;

    // Insert item into cart table
    $sql = "INSERT INTO cart (quantity, user_id, product_id, total_price) 
            VALUES(:quantity, :user_id, :product_id, :total_price)";
    $cartadd = $pdo->prepare($sql);
    $cartadd->execute([
        'quantity' => $quantity, 
        'user_id' => $user_id, 
        'product_id' => $product_id, 
        'total_price' => $total_price
    ]);

    // Create a notification message
    $message = "You added <b>" . $product['title'] . "</b> to your cart.";
    $image = $product['image']; // Get product image for notification
    $notif_type = "cart";
    $status = "unread"; // Default notification status

    // Insert notification into the notifications table
    $notif_sql = "INSERT INTO notifications (user_id, message, type, image, status, created_at) 
                  VALUES (:user_id, :message, :notif_type, :image, :status, NOW())";
    $notif_stmt = $pdo->prepare($notif_sql);
    $notif_stmt->execute([
        'user_id' => $user_id, 
        'message' => $message, 
        'notif_type' => $notif_type, 
        'image' => $image, 
        'status' => $status
    ]);

    echo json_encode(["message" => "Added to cart", "notification" => "Notification sent"]);

    $pdo = null;

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(["error" => "Database error: " . $e->getMessage()]);
}

?>
