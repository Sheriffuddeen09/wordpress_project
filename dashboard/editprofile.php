<?php

require_once './wp-load.php';
require_once './database/function.php';

global $conn;
$user_id = get_current_user_id(); // Get logged-in user ID

if (!$user_id) {
    wp_redirect(home_url('/login'));
    exit;
}

// Fetch user data
$user = get_userdata($user_id);
$firstname = get_user_meta($user_id, 'first_name', true);
$lastname = get_user_meta($user_id, 'last_name', true);
$phone = get_user_meta($user_id, 'phone', true);
$profile_image = get_user_meta($user_id, 'profile_image', true);
$email = $user->user_email;

$message = "";
$status_code = 200;

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_profile'])) {
    $new_firstname = sanitize_text_field($_POST['firstname']);
    $new_lastname = sanitize_text_field($_POST['lastname']);
    $new_phone = sanitize_text_field($_POST['phone']);
    $new_email = sanitize_email($_POST['email']);
    $new_password = !empty($_POST['password']) ? sanitize_text_field($_POST['password']) : null;
    $profile_image_url = $profile_image;

    // Handle profile image upload
    if (!empty($_FILES['profile_image']['name']) && $_FILES['profile_image']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = wp_upload_dir();
        $file_name = time() . '-' . basename($_FILES['profile_image']['name']);
        $image_path = $upload_dir['path'] . '/' . $file_name;

        if (move_uploaded_file($_FILES['profile_image']['tmp_name'], $image_path)) {
            $profile_image_url = $upload_dir['url'] . '/' . $file_name;
            update_user_meta($user_id, 'profile_image', $profile_image_url);
        }
    }

    // ** Update user meta data (wp_usermeta table) **
    update_user_meta($user_id, 'first_name', $new_firstname);
    update_user_meta($user_id, 'last_name', $new_lastname);
    update_user_meta($user_id, 'phone', $new_phone);

    // ** Update email only if it's different and not already taken **
    if ($new_email !== $email) {
        if (!email_exists($new_email)) {
            wp_update_user(['ID' => $user_id, 'user_email' => $new_email]);
        } else {
            $message = "Error: Email is already in use.";
            $status_code = 400;
        }
    }

    // ** Update password if provided **
    if ($new_password) {
        wp_set_password($new_password, $user_id);
    }

    // ** Update wp_users table with first name and last name **
    $user_update = wp_update_user([
        'ID'           => $user_id,
        'first_name'   => $new_firstname,
        'last_name'    => $new_lastname,
    ]);

    if (is_wp_error($user_update)) {
        $message = "Error updating profile.";
        $status_code = 500;
    } else {
        $message = "Profile updated successfully.";
        $status_code = 200;
    }
}
http_response_code($status_code);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Profile</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="flex justify-center items-center min-h-screen bg-gray-100">
    <div class="bg-white px-8 pt-4 pb-8 rounded-xl border my-10 border-green-200 shadow-lg max-w-sm w-full flex flex-col mx-auto">
        <h1 class="text-2xl text-green-400 font-serif text-center">Update Profile</h1>
        <form method="POST" enctype="multipart/form-data" class="flex flex-col">
            <label class="mt-2 text-sm">Firstname:</label>
            <input type="text" name="firstname" value="<?php echo esc_attr($firstname); ?>" class="border text-sm p-2 rounded" required>

            <label class="mt-2 text-sm">Lastname:</label>
            <input type="text" name="lastname" value="<?php echo esc_attr($lastname); ?>" class="border text-sm p-2 rounded" required>

            <label class="mt-2 text-sm">Email:</label>
            <input type="email" name="email" value="<?php echo esc_attr($email); ?>" class="border text-sm p-2 rounded" required>

            <label class="mt-2 text-sm">Phone:</label>
            <input type="text" name="phone" value="<?php echo esc_attr($phone); ?>" class="border text-sm p-2 rounded" required>

            <label class="mt-2 text-sm">New Password (optional):</label>
            <input type="password" name="password" class="border text-sm p-2 rounded">

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

            <button type="submit" name="update_profile" class="mt-4 text-sm bg-green-500 text-white p-2 rounded">Update</button>
        </form>
        
        <?php if (!empty($message)): ?>
            <p class="text-green-500 text-center mt-3"> <?php echo esc_html($message); ?> </p>
        <?php endif; ?>
    </div>

    <script>
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
</body>
</html>  
