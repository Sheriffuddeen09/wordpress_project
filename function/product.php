<?php
require_once '../database/function.php';

header("Content-Type: application/json");

global $conn;

error_reporting(E_ALL);
ini_set('display_errors', 1);

$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$category = isset($_GET['category']) ? trim($_GET['category']) : '';

try {
    global $conn;    

    $query = "SELECT * FROM products WHERE 1";
    $params = [];

    if (!empty($category) && $category !== "All") {
        $query .= " AND category = ?";
        $params[] = $category;
    }

    if (!empty($search)) {
        $query .= " AND (title LIKE ? OR maintenance LIKE ?)";
        $params[] = "%$search%";
        $params[] = "%$search%";
    }

    $stmt = $conn->prepare($query);

    if (!empty($params)) {
        $stmt->bind_param(str_repeat("s", count($params)), ...$params);
    }

    $stmt->execute();
    $result = $stmt->get_result();
    
    $products = $result->fetch_all(MYSQLI_ASSOC); // Corrected fetch method for MySQLi

    echo json_encode(["success" => true, "message" => $products], JSON_PRETTY_PRINT);
    exit;
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(["error" => "Database error: " . $e->getMessage()]);
    exit;
}

?>
