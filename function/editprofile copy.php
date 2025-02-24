<?php

require_once dirname(__DIR__) . '/wp-load.php';
require_once '../database/function.php';

header('Content-Type: application/json');

$user_id = get_current_user_id(); // Get logged-in user ID

if (!$user_id) {
    echo json_encode(["message" => "Unauthorized access. Please log in."]);
    http_response_code(401);
    exit;
}

// Fetch user data
$user = get_userdata($user_id);
$firstname = get_user_meta($user_id, 'first_name', true);
$lastname = get_user_meta($user_id, 'last_name', true);
$phone = get_user_meta($user_id, 'phone', true);
$profile_image = get_user_meta($user_id, 'profile_image', true);
$status_code = 200;
$message = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $new_firstname = sanitize_text_field($_POST['firstname']);
    $new_lastname = sanitize_text_field($_POST['lastname']);
    $new_phone = sanitize_text_field($_POST['phone']);
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

    // Update user meta data
    update_user_meta($user_id, 'first_name', $new_firstname);
    update_user_meta($user_id, 'last_name', $new_lastname);
    update_user_meta($user_id, 'phone', $new_phone);

    // Update password if provided
    if ($new_password) {
        wp_set_password($new_password, $user_id);
    }

    // Update wp_users table with first name and last name
    $user_update = wp_update_user([
        'ID'         => $user_id,
        'first_name' => $new_firstname,
        'last_name'  => $new_lastname,
    ]);

    if (is_wp_error($user_update)) {
        $message = "Error updating profile.";
        $status_code = 500;
    } else {
        $message = "Profile updated successfully.";
    }
}

http_response_code($status_code);
echo json_encode(["message" => $message]);
?>
