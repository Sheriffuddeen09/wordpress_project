<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/mywebsite/wp-load.php';
require_once '../database/function.php';

header("Content-Type: application/json");

if (!is_user_logged_in()) {
    echo json_encode(["error" => "User not logged in", "debug" => $_COOKIE]);
    exit;
}

global $conn;

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

if (!is_user_logged_in()) {
    echo json_encode(["error" => "User not logged in"]);
    exit;
}

$current_user = wp_get_current_user();
$user_id = $current_user->ID;

// Handle profile image upload
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_FILES['profile_image'])) {
    $upload_dir = __DIR__ . '/uploads/';

    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }

    $image_name = time() . '_' . basename($_FILES['profile_image']['name']);
    $image_path = $upload_dir . $image_name;

    if (move_uploaded_file($_FILES['profile_image']['tmp_name'], $image_path)) {
        $stmt = $conn->prepare("UPDATE users SET profile_image = :profile_image WHERE id = :id");
        $stmt->execute([
            'profile_image' => $image_name,
            'id' => $user_id
        ]);

        echo json_encode([
            "success" => true,
            "message" => "Profile image updated!",
            "profile_image" => "uploads/{$image_name}"
        ]);
    } else {
        echo json_encode(["error" => "Failed to upload image"]);
    }
    exit;
}

// Fetch user details
$stmt = $conn->prepare("SELECT firstname, lastname, email, phone, profile_image FROM users WHERE id = :id");
$stmt->execute(['id' => $user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    echo json_encode(["error" => "User not found"]);
    exit;
}

// Logout function
if (isset($_GET['logout'])) {
    wp_logout();
    echo json_encode(["success" => true, "message" => "Logged out"]);
    exit;
}

echo json_encode([
    "success" => true,
    "user" => [
        "firstname" => $user['firstname'] ?? '',
        "lastname" => $user['lastname'] ?? '',
        "email" => $user['email'] ?? '',
        "phone" => $user['phone'] ?? '',
        "profile_image" => !empty($user['profile_image']) ? "uploads/{$user['profile_image']}" : "default-profile.png"
    ]
]);
exit;
?>
