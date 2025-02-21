<?php

require_once './wp-load.php';
require_once './database/function.php';

global $conn;
$tablename = "users";
$message = "";
$status_code = 200;

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['register'])) {
    $firstname = sanitize_text_field($_POST['firstname']);
    $lastname = sanitize_text_field($_POST['lastname']);
    $email = sanitize_email($_POST['email']);
    $phone = sanitize_text_field($_POST['phone']);
    $password = sanitize_text_field($_POST['password']);
    $profile_image = $_FILES['profile_image'];

    if (email_exists($email)) {
        $message = "Email is already registered.";
        $status_code = 409; // Conflict
    } else {
        $user_id = wp_create_user($email, $password, $email);

        if (!is_wp_error($user_id)) {
            update_user_meta($user_id, 'first_name', $firstname);
            update_user_meta($user_id, 'last_name', $lastname);
            update_user_meta($user_id, 'phone', $phone);

            $profile_image_url = "";
            if ($profile_image && $profile_image['error'] === UPLOAD_ERR_OK) {
                $upload_dir = wp_upload_dir();
                $file_name = time() . '-' . basename($profile_image['name']);
                $image_path = $upload_dir['path'] . '/' . $file_name;

                if (move_uploaded_file($profile_image['tmp_name'], $image_path)) {
                    $profile_image_url = $upload_dir['url'] . '/' . $file_name;
                }
            }

            // Prepare the SQL statement to insert data into the 'users' table
            $sql = "INSERT INTO $tablename (email, profile_image, password, phone, firstname, lastname) 
                    VALUES (?, ?, ?, ?, ?, ?)";

            $stmt = $conn->prepare($sql);

            // Hash the password before saving
            $hashed_password = wp_hash_password($password);

            // Bind parameters to the prepared statement
            $stmt->bind_param("ssssss", $email, $profile_image_url, $hashed_password, $phone, $firstname, $lastname);

            // Execute the query
            if ($stmt->execute()) {
                http_response_code(201); // Created
                wp_redirect(home_url('/login'));
                exit;
            } else {
                $message = "An error occurred during registration. Please try again.";
                $status_code = 500; // Internal Server Error
            }

            $stmt->close();
        } else {
            $message = "An error occurred during registration. Please try again.";
            $status_code = 500; // Internal Server Error
        }
    }
}
http_response_code($status_code);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <metaonkeyup="checkInputs()" name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <script>
        function checkInputs() {
            
            let email = document.getElementById("email").value.trim();
            let password = document.getElementById("password").value.trim();
            let firstname = document.getElementById("firstname").value.trim();
            let lastname = document.getElementById("lastname").value.trim();
            let phone = document.getElementById("phone").value.trim();
            let button = document.getElementById("register-btn");
            button.disabled = !(email && password && firstname && lastname && phone);



        }
        function previewFile() {
            let fileInput = document.getElementById('profile_image');
            let previewText = document.getElementById('file-preview');
            
            if (fileInput.files.length > 0) {
                previewText.innerText = "Selected: " + fileInput.files[0].name;
            } else {
                previewText.innerText = "No file chosen";
            }
        }

    </script>
</head>

<body class="flex justify-center items-center sm:min-h-screen bg-gray-100">
    <div class="bg-white px-8 pt-4 pb-8 rounded-xl border border-3 mb-7 border-green-200 shadow-lg max-w-sm sm:w-full w-72 flex justify-center items-center flex-col mx-auto">
    <form method="POST" enctype="multipart/form-data" class="flex flex-col mx-auto justify-center items-center">
        
    <h1 class="sm:text-3xl text-xl text-center text-green-400 font-serif"><span class="sm:text-2xl text-xl text-black mt-2 text-center font-bold font-roboto">Create Account</span></h1>

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

    <p class="text-xs text-black my-3 font-bold mx-auto text-center font-roboto"> 
    Enter your order number and email address below to view your rental details. </p>
        <!-- Registration Form Fields -->
        <div class="sm:inline-flex sm:flex-nowrap mx-auto translate-x-3 gap-1">
        <div class="mb-4">
            <label for="firstname" class="block text-black font-roboto mb-2   font-bold text-sm">Firstname:</label>
            <input type="text" onkeyup="checkInputs()" name="firstname" id="firstname" required class="border-2 border-green-200 sm:w-40 w-64  px-2 p-1 rounded-lg text-black outline-none" value="<?php echo isset($firstname) ? esc_attr($firstname) : ''; ?>" />
        </div>

        <div class="mb-4">
            <label for="lastname" class="block text-sm mb-2 font-bold font-roboto text-black font-Cambria">Lastname:</label>
            <input type="text"onkeyup="checkInputs()" name="lastname" id="lastname" required class="border-2 border-green-200 sm:w-40 w-64  px-2 p-1 rounded-lg text-black outline-none" value="<?php echo isset($lastname) ? esc_attr($lastname) : ''; ?>" />
        </div>
        </div>

        <div class="mb-4">
            <label for="email" class="block text-sm mb-2  -mt-2 font-bold font-roboto text-black font-Cambria">Email Address:</label>
            <input type="email"onkeyup="checkInputs()" name="email" id="email" required class="border-2 border-green-200 sm:w-80 w-64  px-2 p-1 rounded-lg text-black outline-none" value="<?php echo isset($email) ? esc_attr($email) : ''; ?>" />
        </div>

        <div class="mb-4">
            <label for="phone" class="block text-sm mb-2  -mt-2 font-bold font-roboto text-black font-Cambria">Phone Number:</label>
            <input type="text"onkeyup="checkInputs()" name="phone" id="phone" required class="border-2 border-green-200 sm:w-80 w-64  px-2 p-1 rounded-lg text-black outline-none" value="<?php echo isset($phone) ? esc_attr($phone) : ''; ?>" />
        </div>

        <div class="mb-4">
            <label for="password" class="block text-sm mb-2  -mt-2 font-bold font-roboto text-black font-Cambria">Password:</label>
            <input type="password"onkeyup="checkInputs()" name="password" id="password" required class="border-2 border-green-200 sm:w-80 w-64  px-2 p-1 rounded-lg text-black outline-none" />
        </div>

        <div class="mb-4 -mt-4">
        <label class="block text-sm font-bold mt-2 mb-2">Profile Image:</label>
            <div class="relative flex jsutify-center text-sm items-center">
                <input type="file" name="profile_image" id="profile_image" accept="image/*" onchange="previewFile()" class="hidden text-sm" />
                <label for="profile_image" class="cursor-pointer sm:w-80 w-64 mx-auto bg-green-500 text-white px-4 py-2 rounded flex items-center">
                    <i class="fas fa-upload mr-2 text-sm"></i> Choose File
                </label>
            </div>
            <p id="file-preview" class="text-xs text-gray-500 mt-1">No file chosen</p>
            
        </div>

        <button type="submit" name="register" class="border-2  register-btn bg-green-500  border-green-500 sm:w-80 w-64  px-2 p-1 text-sm font-bold rounded-lg text-white outline-none">Register</button>
        <p class="text-xs mt-3 -mb-3 font-bold"> Don’t have a Rover account?
                <span>
                    <a href="register.php" class="text-green-300 text-sm"> Sign up now</a>
                </span>
            </p>
         <?php if (!empty($message)) : ?>
            <p class="text-red-500 text-center mt-3"> <?php echo esc_html($message); ?> </p>
        <?php endif; ?>
    </form>
</div>
</body>
