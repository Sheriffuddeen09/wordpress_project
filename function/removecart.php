<?php
require_once '../database/function.php';

header("Content-Type: application/json");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'DELETE') {
    http_response_code(405);
    echo json_encode(["error" => "Only DELETE requests are allowed"]);
    exit;
}

// Read input data
$data = json_decode(file_get_contents("php://input"), true);

// Debugging: Log received data
file_put_contents("debug_log.txt", print_r($data, true)); 

// Validate required fields
if (!isset($data['user_id']) || !isset($data['cart_id'])) {
    http_response_code(400);
    echo json_encode(["error" => "Missing user_id or cart_id", "received_data" => $data]);
    exit;
}

$user_id = intval($data['user_id']);
$cart_id = intval($data['cart_id']);

try {
    global $pdo;

    // Check if product exists in cart
    $stmt = $pdo->prepare("SELECT p.title FROM cart c JOIN products p ON c.product_id = p.id WHERE c.id = ? AND c.user_id = ?");
    $stmt->execute(['cart_id' => $cart_id, 'user_id' => $user_id]);
    $product = $stmt->fetch(MYSQLI_ASSOC);

    if (!$product) {
        echo json_encode(["success" => false, "error" => "Cart item not found"]);
        exit;
    }

    // Delete item from cart
    $stmtDelete = $pdo->prepare("DELETE FROM cart WHERE id = ? AND user_id = ?");
    $stmtDelete->execute(['cart_id' => $cart_id, 'user_id' => $user_id]);

    if ($stmtDelete->rowCount() > 0) {
        // Add notification
        $message = "You removed " . $product['title'] . " from your cart.";
        $stmtNotif = $pdo->prepare("INSERT INTO notifications (user_id, message, type) VALUES (:user_id, :message, 'cart_remove')");
        $stmtNotif->execute(['user_id' => $user_id, 'message' => $message]);

        echo json_encode(["success" => true, "message" => "Cart item deleted successfully"]);
    } else {
        echo json_encode(["success" => false, "error" => "Failed to delete cart item"]);
    }
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(["error" => "Database error: " . $e->getMessage()]);
}
?>
