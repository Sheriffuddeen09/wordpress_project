<?php
ob_start(); // Start output buffering
require_once './wp-load.php';
require_once './database/function.php';

if (!is_user_logged_in()) {
    wp_redirect("login.php");
    exit();
}

global $conn;
$user_id = get_current_user_id();

if (!$user_id) {
    wp_redirect("login.php");
    exit();
}

// Fetch user details from WordPress database (wp_users and wp_usermeta)
$user = get_userdata($user_id);

if (!$user) {
    wp_redirect("login.php");
    exit();
}

// Fetch additional user details from custom `users` table (if needed)
$sql = "SELECT firstname, lastname, phone, profile_image FROM users WHERE email = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $user->user_email);
$stmt->execute();
$result = $stmt->get_result();
$custom_user = $result->fetch_assoc();

// Merge WordPress and custom user data
$user_data = [
    'id' => $user->ID,
    'email' => $user->user_email,
    'firstname' => $custom_user['firstname'] ?? $user->first_name,
    'lastname' => $custom_user['lastname'] ?? $user->last_name,
    'phone' => $custom_user['phone'] ?? '',
    'profile_image' => $custom_user['profile_image'] ?? '',
];

// Handle logout
if (isset($_POST['logout'])) {
    wp_logout();
    wp_redirect("login.php");
    exit();
}

ob_end_flush(); // End output buffering
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <script>
       
    </script>
</head>
<div>
            <div class="my-10  flex w-72 sm:w-full sm:justify-center scroll-wid rounded-lg scrollb scroll-p-0 scroll-smooth scrollbar scrollbar-thumb-blue-300 sm:scrollbar-thumb-transparent  scrollbar-thin scrollbar-track-white sm:scrollbar-track-transparent  mx-auto my-2 gap-3">
            <a href='/dashboard'>
            <button style="background-color: #FFCCEA;" class='bg-[#FFCCEA] p-2 sm:w-28 w-32 rounded items-center gap-2 font-bold text-sm text-[#D2016A] flex justify-center '> <img src="<?php echo esc_url(site_url('/dashboard/image/Vector (3).png')); ?>" alt="Logo" class='h-4 w-4'> <p> Profile </p></button>
            </a>
            <a href='/cart'>
            <button style="background-color: #FFCCEA;" class='bg-[#FFCCEA] p-2 w-36 rounded items-center gap-2 font-bold text-sm text-[#D2016A] flex justify-center '> <img src="<?php echo esc_url(site_url('/dashboard/image/Vector (4).png')); ?>" alt="Logo" class='h-4 w-4'> <p> My Wishlist </p>
            </button>
            </a>
            <a href='/order' class="hidden sm:block">
            <button style="background-color: #FFCCEA;" class='bg-[#FFCCEA] p-2 w-40 rounded items-center gap-2 font-bold text-sm text-[#D2016A] flex justify-center '> <img src="<?php echo esc_url(site_url('/dashboard/image/Vector (5).png')); ?>" alt="Logo" class='h-4 w-4'> <p> Order History </p>
            </button>
            </a>
            <a href='/contact'>
            <button style="background-color: #FFCCEA;" class='bg-[#FFCCEA] p-2 sm:w-36 w-40 rounded items-center gap-2 font-bold text-sm text-[#D2016A] flex justify-center '> <img src="<?php echo esc_url(site_url('/dashboard/image/Vector (6).png')); ?>" alt="Logo" class='h-4 w-4'> <p> Help Center </p>
            </button>
            </a>
            <button onClick={handleLogout} style="background-color: #FFCCEA;" class='bg-[#FFCCEA] py-2 px-6 sm:px-2 w-40 rounded items-center gap-2 font-bold text-sm text-[#D2016A] flex whitespace-nowrap justify-center '> <img src="<?php echo esc_url(site_url('/dashboard/image/Vector (7).png')); ?>" alt="Logo" class='h-4 w-4'> <p> Log Out </p></button>
          
            </div>
<div  class="mb-10 flex gap-4 flex-row my-8 flex-wrap justify-center">
    <div class="flex justify-center mt-10 max-w-sm w-96 bg-blue-200 flex-col items-center" style="background-image: url('<?php echo esc_url(site_url('/dashboard/image/Untitled design (10) 2.png')); ?>');">
            <?php if (!empty($user['profile_image'])): ?>
                <img src="uploads/<?php echo htmlspecialchars($user['profile_image']); ?>" class="w-24 h-24 rounded-full mx-auto" alt="Profile">
            <?php endif; ?>
            <a href="edit-profile.php" class="text-blue-500 text-center text-sm font-bold mt-2 block">Edit Profile</a>
            <p class="text-sm mb-2 font-bold"><?php echo htmlspecialchars($user['firstname'] . " " . $user['lastname']); ?></p>
            <p class="text-sm mb-2 font-bold">Email: <?php echo htmlspecialchars($user['email']); ?></p>
            <p class="text-sm mb-2 font-bold">Phone: <?php echo htmlspecialchars($user['phone']); ?></p>
        </div>
            
    <div class="flex flex-col gap-2">
            <a href='/notification'>
            <div style="background-color: #CEEEE9;" class=" sm:w-80 w-72 rounded p-4 border border-2 border-blue-200">
                <div class="inline-flex items-center gap-3  ">
                <img src="<?php echo esc_url(site_url('/dashboard/image/Vector (9).png')); ?>" alt="Logo" class='h-4 w-3'>
                    <p class="font-bold  text-sm">Settings</p>
                </div>
                <p class="text-sm font-bold my-2"> Notification</p>
                <p class=" w-64 text-xs"> We send SMS messages for booking-related notifications. You can select the notifications you would like to receive.</p>
            </div>
        </a>
            <div style="background-color: #CEEEE9;" class=" sm:w-80 w-72 rounded px-4 py-2 border border-2 border-blue-200">
                <div class="inline-flex items-center gap-3  ">
                <img src="<?php echo esc_url(site_url('/dashboard/image/Pets--Streamline-Rounded-Material 1.png')); ?>" alt="Logo" class='h-4 w-3'>

                    <p class="font-bold  text-sm">Pets</p>
                </div>
                <p class="text-sm my-2 font-bold"> Tell Us About Your Pets</p>
                <p class=" w-64 sm:w-72 text-xs"> Share details about your furry companions to help us provide the perfect rental experience! Add them one by one to ensure we understand their unique needs and preferences.</p>
                <p class=" w-64 sm:w-72 my-2 text-xs"> To ensure safety and compatibility with the rental environment.</p>
                <a href='/shop'><p class="text-pink-700 text-sm">View Pets</p></a>
            </div>

            <div style="background-color: #CEEEE9;" class=" sm:w-80 w-72 rounded px-4 pb-2 border border-2 border-blue-200">
                <a href='/payment'>
                <div class="inline-flex items-center gap-3  ">
                <img src="<?php echo esc_url(site_url('/dashboard/image/Vector (8).png')); ?>" alt="Logo" class='h-4 w-3'>
                    <p class="font-bold  text-sm">Payment Methods</p>
                </div>
                <p class=" w-64 text-xs"> We offer a variety of secure and convenient payment options to make your rental experience hassle-free. Choose the method that works best for you:</p>
                <div class="mt-2 flex flex-row justify-around">
                <img src="<?php echo esc_url(site_url('/dashboard/image/Frame 144.png')); ?>" alt="Logo" class='h-4 w-16'>
                <img src="<?php echo esc_url(site_url('/dashboard/image/download (3) 1.png')); ?>" alt="Logo" class='h-4 w-16'>
                <img src="<?php echo esc_url(site_url('/dashboard/image/Frame 146.png')); ?>" alt="Logo" class='h-4 w-16'>
            
                </div>
                </a>
            </div>
    </div>
            
 </div>