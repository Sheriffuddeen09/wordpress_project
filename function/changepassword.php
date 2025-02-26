<?php
session_start();
print_r($_SESSION); // Debugging - See session data

header("Access-Control-Allow-Origin: http://localhost");
header("Access-Control-Allow-Credentials: true");
header("Content-Type: application/json");

$mysqli = new mysqli("localhost", "root", "", "pet_database");

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(["error" => "Unauthorized"]);
    exit();
}

$user_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // Fetch user data
    $query = "SELECT firstname, lastname, phone, profile_image FROM users WHERE id = ?";
    $stmt = $mysqli->prepare($query);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    echo json_encode(["user" => $user]);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Update user profile
    $firstname = $_POST['firstname'];
    $lastname = $_POST['lastname'];
    $phone = $_POST['phone'];
    $profile_image = null;

    // Handle image upload
    if (isset($_FILES['profile_image']) && $_FILES['profile_image']['size'] > 0) {
        $targetDir = "uploads/";
        $targetFile = $targetDir . basename($_FILES['profile_image']['name']);
        move_uploaded_file($_FILES['profile_image']['tmp_name'], $targetFile);
        $profile_image = $targetFile;
    }

    // Update user data in database
    if ($profile_image) {
        $query = "UPDATE users SET firstname = ?, lastname = ?, phone = ?, profile_image = ? WHERE id = ?";
        $stmt = $mysqli->prepare($query);
        $stmt->bind_param("ssssi", $firstname, $lastname, $phone, $profile_image, $user_id);
    } else {
        $query = "UPDATE users SET firstname = ?, lastname = ?, phone = ? WHERE id = ?";
        $stmt = $mysqli->prepare($query);
        $stmt->bind_param("sssi", $firstname, $lastname, $phone, $user_id);
    }

    if ($stmt->execute()) {
        echo json_encode(["success" => true, "message" => "Profile updated successfully!"]);
    } else {
        echo json_encode(["error" => "Failed to update profile."]);
    }

    exit();
}
?>
