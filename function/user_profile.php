<?php
session_start();
require_once dirname(__DIR__) . '/wp-load.php';
require_once '../database/function.php';

header('Content-Type: application/json');

// ✅ Handle logout first
if (isset($_GET['logout']) && $_GET['logout'] === 'true') {
    session_destroy();
    echo json_encode(["success" => true, "message" => "Logged out successfully"]);
    exit;
}

// ✅ Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(["success" => false, "error" => "User not logged in"]);
    exit;
}

$user_id = $_SESSION['user_id'];

// ✅ Fetch user profile
$sql = "SELECT id, firstname, lastname, email, phone, profile_image FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();

    // ✅ Fix profile image URL
    if (!empty($user['profile_image'])) {
        // If the image path already contains "http", use it as is
        if (!preg_match("/^http/", $user['profile_image'])) {
            $user['profile_image'] = "http://localhost/mywebsite/uploads/" . basename($user['profile_image']);
        }
    }
    

    // ✅ Return full user data
    echo json_encode(["success" => true, "user" => $user]);
} else {
    echo json_encode(["success" => false, "error" => "User not found"]);
}

exit;
?>
