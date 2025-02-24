<?php
require_once '../database/function.php';

header("Content-Type: application/json");

global $conn;
error_reporting(E_ALL);
ini_set('display_errors', 1);

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

try {
    if ($id > 0) {
        // Ensure $conn is a MySQLi instance
        if (!$conn instanceof mysqli) {
            throw new Exception("Database connection error: \$conn is not a MySQLi instance.");
        }

        // Fetch product
        $query = "SELECT * FROM homeproduct WHERE id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $product = $result->fetch_assoc();

        if ($product) {
            // Fetch reviews
            $reviewQuery = "SELECT r.*, u.firstname, u.lastname, u.profile_image 
                            FROM reviews r 
                            JOIN users u ON r.user_id = u.id 
                            WHERE r.product_id = ? 
                            ORDER BY r.created_at DESC";

            $reviewProduct = $conn->prepare($reviewQuery);
            $reviewProduct->bind_param("i", $id);
            $reviewProduct->execute();
            $reviewResult = $reviewProduct->get_result();
            $reviews = $reviewResult->fetch_all(MYSQLI_ASSOC);

            echo json_encode(["success" => true, "product" => $product, "reviews" => $reviews], JSON_PRETTY_PRINT);
        } else {
            echo json_encode(["success" => false, "message" => "Product not found"]);
        }
    } else {
        echo json_encode(["success" => false, "message" => "Invalid product ID"]);
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(["error" => $e->getMessage()]);
}
?>
