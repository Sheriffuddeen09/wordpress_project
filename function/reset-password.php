<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

ob_start();
session_start();

require_once dirname(__DIR__) . '/wp-load.php';
require_once '../database/function.php';

header('Content-Type: application/json');

global $conn; 

if (!isset($conn)) {
    $conn = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
    if (mysqli_connect_errno()) {
        die(json_encode(["status" => "error", "message" => "Failed to connect to MySQL: " . mysqli_connect_error()]));
    }
}

// Debugging - Log received POST data
error_log("Received POST Data: " . print_r($_POST, true));

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['reset_request'])) {
    if (empty($_POST['email_reset'])) {
        echo json_encode(["status" => "error", "message" => "Email field is required."]);
        exit;
    }

    $email = mysqli_real_escape_string($conn, $_POST['email_reset']);
    
    error_log("Processing password reset for: " . $email); // Debug email

    $query = "SELECT * FROM users WHERE email = '$email'";
    $result = mysqli_query($conn, $query);
    
    if (mysqli_num_rows($result) > 0) {
        $token = bin2hex(random_bytes(32));
        $expiry = date('Y-m-d H:i:s', strtotime('+1 hour'));

        mysqli_query($conn, "UPDATE users SET token = '$token' WHERE email = '$email'");
        $reset_url = home_url("formdata/confirm_reset/?token=$token&email=" . urlencode($email));

        $subject = "Password Reset Request";
        $message = "Click here to reset your password: $reset_url";
        wp_mail($email, $subject, $message);

        echo json_encode(["status" => "success", "message" => "A password reset link has been sent to your email."]);
    } else {
        echo json_encode(["status" => "error", "message" => "No user found with that email."]);
    }
    exit;
}


// Handle New Password Submission (Step 2)
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['reset_password'])) {
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $token = mysqli_real_escape_string($conn, $_POST['token']);
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    if ($new_password !== $confirm_password) {
        echo json_encode(["status" => "error", "message" => "Passwords do not match."]);
        exit;
    }

    $new_password_hashed = password_hash($new_password, PASSWORD_BCRYPT);
    $query = "SELECT * FROM users WHERE email = '$email' AND token = '$token'";
    $result = mysqli_query($conn, $query);
    
    if (mysqli_num_rows($result) > 0) {
        mysqli_query($conn, "UPDATE users SET password = '$new_password_hashed', token = NULL WHERE email = '$email'");
        echo json_encode(["status" => "success", "message" => "Your password has been reset successfully!", "redirect" => home_url('/login/')]);
    } else {
        echo json_encode(["status" => "error", "message" => "Invalid or expired reset link."]);
    }
    exit;
}
