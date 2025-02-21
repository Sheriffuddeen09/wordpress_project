<?php
// Database connection details
require_once './database/function.php';
// Start session if not already started
if (!session_id()) {
    session_start();
}

global $conn;

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['login'])) {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    // Check if email and password are provided
    if (empty($email) || empty($password)) {
        http_response_code(400); // Bad Request
        $message = "Email and Password are required";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        http_response_code(400); // Bad Request
        $message = "Invalid email format";
    } else {
        // Prevent SQL injection
        $email = $conn->real_escape_string($email);

        // Fetch user by email from your custom users table
        $query = "SELECT * FROM users WHERE email = '$email'";
        $result = $conn->query($query);

        if ($result->num_rows === 0) {
            http_response_code(404); // Not Found
            $message = "User not found";
        } else {
            $user = $result->fetch_assoc();

            // Use password_verify() to check the hashed password
            if (!password_verify($password, $user['password'])) {
                http_response_code(401); // Unauthorized
                $message = "Invalid password";
            } else {
                // Store login details in session
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_email'] = $user['email'];
                $_SESSION['login_time'] = time();

                // Redirect to dashboard
                header("Location: dashboard.php");
                exit;
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <script>
        function checkInputs() {
            let email = document.getElementById("email").value.trim();
            let password = document.getElementById("password").value.trim();
            let button = document.getElementById("login-btn");
            button.disabled = !(email && password);
        }
    </script>
</head>
<body class="flex justify-center items-center min-h-screen mx-auto">
    <div class="bg-white p-5 rounded-xl border my-10 border-3 border-pink-700 mb-7 flex-col flex justify-center items-center mx-auto shadow-lg max-w-sm w-72 sm:w-full">
        <h2 class="text-2xl font-bold text-center mb-6">Sign in</h2>

        <!-- Social Login Buttons -->
        <div class="flex flex-col space-y-3">
    <!-- Google Login -->
    <a href="<?php echo esc_url( site_url('?action=social_login&provider=google') ); ?>" 
    class="sm:w-72 w-64 flex items-center justify-center gap-2 bg-white border border-gray-300 text-black px-6 py-2 rounded-xl shadow-md text-sm hover:bg-gray-100">
        <i class="fab fa-google text-red-500"></i> Sign in with Google
    </a>

    <!-- Facebook Login -->
    <a href="<?php echo esc_url( site_url('?action=social_login&provider=facebook') ); ?>" 
    class="sm:w-72 w-64 flex items-center justify-center gap-2 bg-blue-600 text-white px-6 py-2 rounded-xl shadow-md hover:bg-blue-700 text-sm">
        <i class="fab fa-facebook"></i> Login with Facebook
    </a>

    <!-- Apple Login -->
    <a href="<?php echo esc_url( site_url('?action=social_login&provider=apple') ); ?>" 
    class="sm:w-72 w-64 flex items-center justify-center gap-2 bg-black text-white px-6 py-2 rounded-xl shadow-md hover:bg-gray-800 text-sm">
        <i class="fab fa-apple"></i> Continue with Apple
    </a>
</div>


        <!-- Email & Password Login -->
        <form method="POST" class="mt-4">
            <div class="mb-3">
                <label class="block text-black font-bold text-sm">Email Address:</label>
                <input type="email" name="email" id="email" onkeyup="checkInputs()" required
                    class="sm:w-72 w-64  p-2 border-2 text-sm border-pink-700 rounded-md focus:border-blue-500 focus:ring focus:ring-blue-200">
            </div>
            <div class="mb-3 relative">
                <label class="block text-black font-bold text-sm">Password:</label>
                <input type="password" name="password" id="password" onkeyup="checkInputs()" required
                    class="sm:w-72 w-64  p-2 border-2 text-sm border-pink-700 rounded-md focus:border-blue-500 focus:ring focus:ring-blue-200">
            </div>

            <button type="submit" name="login" id="login-btn" disabled
                class="sm:w-72 w-64  bg-pink-700  text-sm text-white p-2 rounded-md ">
                Sign in
            </button>

            <?php if (!empty($message)) : ?>
                <p class="text-red-800 text-center font-bold text-sm mt-3"><?php echo htmlspecialchars($message); ?></p>
            <?php endif; ?>
        </form>

        <div class="text-center mt-4">
            <a href="forgot-password.php" class="text-red-500 text-sm font-semibold">Forgot Password?</a>
        </div>
        <div>
        <p></p>
        <div class="text-center mt-4 text-sm">
            No account? <a href="signup.php" class="text-red-500 font-semibold">Create one</a>
        </div>
    </div>
    </div>
</body>
</html>