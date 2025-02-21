<?php
error_reporting(E_ERROR | E_PARSE);
ob_start();
session_start();

// Database Connection
require_once './wp-config.php'; 
require_once './database/function.php';

global $conn; 

if (!isset($conn)) {
    $conn = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
    if (mysqli_connect_errno()) {
        die("Failed to connect to MySQL: " . mysqli_connect_error());
    }
}

// Handle Password Reset Request (Step 1)
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['reset_request'])) {
    $email = mysqli_real_escape_string($conn, $_POST['email_reset']);
    $query = "SELECT * FROM users WHERE email = '$email'";
    $result = mysqli_query($conn, $query);
    
    if (mysqli_num_rows($result) > 0) {
        $user = mysqli_fetch_assoc($result);
        $token = bin2hex(random_bytes(32));
        $expiry = date('Y-m-d H:i:s', strtotime('+1 hour'));
        
        mysqli_query($conn, "UPDATE users SET token = '$token' WHERE email = '$email'");
        $reset_url = home_url("/reset-password/?token=$token&email=" . urlencode($email));
        
        $subject = "Password Reset Request";
        $message = "Click here to reset your password: $reset_url";
        wp_mail($email, $subject, $message);
        http_response_code(200);
        $message_success = "A password reset link has been sent to your email.";
    } else {
        http_response_code(404);
        $message_error = "No user found with that email.";
    }
}

// Handle New Password Submission (Step 2)
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['reset_password'])) {
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $token = mysqli_real_escape_string($conn, $_POST['token']);
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    // Check if passwords match
    if ($new_password !== $confirm_password) {
        http_response_code(400);
        $message_error = "Passwords do not match.";
    } else {
        $new_password_hashed = password_hash($new_password, PASSWORD_BCRYPT);
    
        $query = "SELECT * FROM users WHERE email = '$email' AND token = '$token'";
        $result = mysqli_query($conn, $query);
        
        if (mysqli_num_rows($result) > 0) {
            mysqli_query($conn, "UPDATE users SET password = '$new_password_hashed', token = NULL WHERE email = '$email'");
            http_response_code(200);
            $message_success = "Your password has been reset successfully! Redirecting to login...";
            echo "<script>setTimeout(() => window.location.href = '" . home_url('/login/') . "', 3000);</script>";
        } else {
            http_response_code(400);
            $message_error = "Invalid or expired reset link.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body>
<div class="flex justify-center items-start sm:mt-20 sm:-mb-52 sm:min-h-screen">
    <div class="bg-white px-8 pt-4 pb-8 rounded-xl border border-3 border-pink-00 shadow-lg max-w-sm sm:w-full w-72">
        <?php if (isset($_GET['token']) && isset($_GET['email'])) : ?>
            <!-- Step 2: Reset Password Form -->
            <form method="POST">
                <input type="hidden" name="email" value="<?php echo esc_attr($_GET['email']); ?>" />
                <input type="hidden" name="token" value="<?php echo esc_attr($_GET['token']); ?>" />
                
                <h2 class="text-2xl font-semibold text-center text-black mb-6">Set New Password</h2>

                <div class="mb-4">
                    <label for="new_password" class="block text-black font-bold mb-2">New Password:</label>
                    <input type="password" name="new_password" id="new_password" placeholder='New password' required class="w-full p-2 border-2 border-pink-500 rounded-xl text-black font-bold" />
                </div>

                <div class="mb-4">
                    <label for="confirm_password" class="block text-black font-bold mb-2">Confirm Password:</label>
                    <input type="password" name="confirm_password" placeholder='Confirm password' id="confirm_password" required class="w-full p-2 border-2 border-pink-500 rounded-xl text-black font-bold" />
                </div>

                <button type="submit" name="reset_password" class="w-full py-2 bg-green-500 text-white rounded-xl hover:bg-green-600">Reset Password</button>
                
                <?php if (isset($message_success)) : ?>
                    <p class="text-green-600 mt-4"><?php echo esc_html($message_success); ?></p>
                <?php elseif (isset($message_error)) : ?>
                    <p class="text-red-600 mt-4"><?php echo esc_html($message_error); ?></p>
                <?php endif; ?>
            </form>
        <?php else : ?>
            <!-- Step 1: Request Reset Form -->
            <form method="POST">
                <h2 class="text-2xl font-semibold text-center text-black mb-6">Reset Your Password</h2>

                <div class="mb-4">
                    <label for="email_reset" class="block font-bold text-black mb-2">Email Address:</label>
                    <input type="email" name="email_reset" placeholder='Enter your email' id="email_reset" required class="w-full p-2 border-2 border-pink-500 rounded-xl text-sm text-black" />
                </div>

                <button type="submit" name="reset_request" class="w-full py-2 text-sm bg-pink-500 text-white rounded-xl hover:bg-pink-700">Send Reset Link</button>
                
                <?php if (isset($message_success)) : ?>
                    <p class="text-green-600 mt-4"><?php echo esc_html($message_success); ?></p>
                <?php elseif (isset($message_error)) : ?>
                    <p class="text-red-600 mt-4"><?php echo esc_html($message_error); ?></p>
                <?php endif; ?>
            </form>
        <?php endif; ?>
    </div>
</div>
</body>
</html>
