<?php

require_once '../database/function.php';

header("Content-Type: application/json");

global $conn;
error_reporting(E_ALL);
ini_set('display_errors', 1);


error_reporting(E_ALL);
ini_set('display_errors', 1);

$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['product_id'], $data['user_id'], $data['rating'], $data['review_text'])) {
    echo json_encode(["success" => false, "message" => "Missing required fields"]);
    exit;
}

$user_id = intval($data['user_id']);
$product_id = intval($data['product_id']);
$rating = intval($data['rating']);
$review_text = trim($data['review_text']);


try {
    global $conn;    

    $query = "INSERT INTO reviews (user_id, product_id, rating, review_text) VALUES (:user_id, :product_id, :rating, :review_text)";
    $stmt = $conn->prepare($query);
    $stmt->execute(['user_id'=>$user_id, 'product_id'=>$product_id, 'rating'=>$rating, 'review_text'=>$review_text]);

    echo json_encode(["success" => true,"message" => "Review submitted successfully"], JSON_PRETTY_PRINT);
    exit;
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(["error" => "Database error: " . $e->getMessage()]);
    exit;
}
?>