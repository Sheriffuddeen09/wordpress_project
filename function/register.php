<?php
require_once dirname(__DIR__) . '/wp-load.php';
require_once '../database/function.php'; // Ensure this file includes the database connection

header("Content-Type: application/json");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Database connection
    global $conn; 

    $firstname = trim($_POST["firstname"]);
    $lastname = trim($_POST["lastname"]);
    $email = filter_var($_POST["email"], FILTER_VALIDATE_EMAIL);
    $phone = trim($_POST["phone"]);
    $password = password_hash($_POST["password"], PASSWORD_BCRYPT);

    if (!$email) {
        echo json_encode(["success" => false, "error" => "Invalid email address."]);
        exit();
    }

    // Check if email already exists
    $checkEmail = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $checkEmail->bind_param("s", $email);
    $checkEmail->execute();
    $checkEmail->store_result();

    if ($checkEmail->num_rows > 0) {
        echo json_encode(["success" => false, "error" => "Email already exists."]);
        exit();
    }
    $checkEmail->close();

    // Handle file upload securely
    $profileImage = "";
    if (!empty($_FILES["profile_image"]["name"])) {
        $targetDir = "../uploads/";
        $fileName = basename($_FILES["profile_image"]["name"]);
        $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

        $allowedExtensions = ["jpg", "jpeg", "png", "gif"];
        if (!in_array($fileExt, $allowedExtensions)) {
            echo json_encode(["success" => false, "error" => "Invalid file type. Only JPG, PNG, GIF allowed."]);
            exit();
        }

        $newFileName = uniqid() . "." . $fileExt;
        $profileImage = $targetDir . $newFileName;

        if (!move_uploaded_file($_FILES["profile_image"]["tmp_name"], $profileImage)) {
            echo json_encode(["success" => false, "error" => "Failed to upload image."]);
            exit();
        }
    }

    // Insert into database
    $query = $conn->prepare("INSERT INTO users (firstname, lastname, email, phone, password, profile_image) VALUES (?, ?, ?, ?, ?, ?)");
    $query->bind_param("ssssss", $firstname, $lastname, $email, $phone, $password, $profileImage);

    if ($query->execute()) {
        echo json_encode(["success" => true, "message" => "Registration successful"]);
    } else {
        echo json_encode(["success" => false, "error" => "Registration failed. Error: " . $conn->error]);
    }

    $query->close();
    $conn->close();
} else {
    echo json_encode(["success" => false, "error" => "Invalid request."]);
}
?>
