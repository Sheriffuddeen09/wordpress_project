<?php
require_once '../database/function.php';

header("Content-Type: application/json");

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT); // Enable MySQL error reporting

global $conn;

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

try {
    if ($id > 0) {
        if (!$conn instanceof mysqli) {
            throw new Exception("Database connection error: \$conn is not a MySQLi instance.");
        }

        // Fetch product details
        $stmt = $conn->prepare("SELECT * FROM homeproduct WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $product = $result->fetch_assoc();

        if ($product) {
            // Fetch reviews
            $reviewStmt = $conn->prepare("SELECT r.*, u.firstname, u.lastname, u.profile_image 
                                          FROM reviews r 
                                          JOIN users u ON r.user_id = u.id 
                                          WHERE r.homeproduct_id = ? 
                                          ORDER BY r.created_at DESC");
            $reviewStmt->bind_param("i", $id);
            $reviewStmt->execute();
            $reviewResult = $reviewStmt->get_result();

            $reviews = [];
            if ($reviewResult->num_rows > 0) {
                $reviews = $reviewResult->fetch_all(MYSQLI_ASSOC);
            }

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
