<?php
require_once dirname(__DIR__) . '/wp-load.php';
require_once '../database/function.php';

header('Content-Type: application/json');

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = trim($_POST["email"]);
    $password = trim($_POST["password"]);

    $stmt = $conn->prepare("SELECT id, password FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        echo json_encode(["status" => "error", "message" => "Email not registered"]);
        exit();
    }

    $user = $result->fetch_assoc();
    
    // Verify password
    if (!password_verify($password, $user["password"])) {
        echo json_encode(["status" => "error", "message" => "Invalid password"]);
        exit();
    }

    // Successful login
    echo json_encode(["status" => "success", "redirect" => "dashboardpage.html"]);
    exit();
}
?>
