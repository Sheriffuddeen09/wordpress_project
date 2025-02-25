<?php

require_once 'database.php'; // Ensure database connection is included

$allowedOrigins = ['http://localhost:3000', 'http://localhost:3001', 'http://another-allowed-origin.com'];

error_reporting(E_ALL);
ini_set('display_errors', 1);


if (isset($_SERVER['HTTP_ORIGIN']) && in_array($_SERVER['HTTP_ORIGIN'], $allowedOrigins)) {
    header("Access-Control-Allow-Origin: " . $_SERVER['HTTP_ORIGIN']);
    header("Access-Control-Allow-Credentials: true");
    header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
    header("Access-Control-Allow-Headers: Content-Type, Authorization");
    header("Access-Control-Max-Age: 86400"); 

}

error_reporting(E_ALL);
ini_set('display_errors', 1);

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;


try {
    global $pdo;    

    if ($id > 0){
    $query = "SELECT * FROM homeproduct WHERE id = :id";
    $stmt = $pdo->prepare($query);
    $stmt->execute(['id' => $id]);
    $product = $stmt->fetch(PDO::FETCH_ASSOC);

    // Debugging: Print JSON response
    if ($product) {


        $reviewQuery  = "SELECT r.*, u.firstname, u.lastname, u.profile_image FROM reviews r JOIN users u ON r.user_id = u.id WHERE r.product_id = :id ORDER BY r.created_at DESC  ";
        $reviewProduct = $pdo->prepare($reviewQuery);
        $reviewProduct->execute(['id'=>$id]);
        $reviews= $reviewProduct->fetchALl(PDO::FETCH_ASSOC);

        echo json_encode(["success" => true, "product" => $product, "reviews"=>$reviews], JSON_PRETTY_PRINT);
    } else {
        echo json_encode(["success" => false, "message" => "Product not found"]);
    }

} else {
    echo json_encode(["success" => false, "message" => "Invalid product ID"]);
}
exit;
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(["error" => "Database error: " . $e->getMessage()]);
    exit;
}
?>
