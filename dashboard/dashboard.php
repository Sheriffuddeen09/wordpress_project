<?php

require_once './wp-load.php';
require_once './database/function.php';

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

// ✅ Fix missing semicolon & incorrect input handling
$userId = trim($_POST['user_id'] ?? '');  

    try{
        global $pdo;

    if (!isset($_FILES['profile_image']) || !$userId) {
        http_response_code(400);
        echo json_encode(['error' => 'Image and user ID are required']);
        exit;
    }

    $uploadDir = __DIR__ . '/upload/'; 
    
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }


    $imageName = time() . '_' . basename($_FILES['profile_image']['name']);

    $imagePath =  $uploadDir . $imageName;

        if(move_uploaded_file($_FILES['profile_image']['tmp_name'], $imagePath)){

        $query = "UPDATE users SET profile_image = :profile_image WHERE id = :id";
        $stmt =$pdo->prepare($query);
        $stmt->execute(['profile_image'=>$imagePath, 'id'=>$userId]);

        echo json_encode([
            "success" => true,
            "message" => "Image uploaded successfully!", 
            "profile_image" => $imageName]);
        }
        else {
            http_response_code(500);
            echo json_encode(["error" => "Failed to upload image."]);
        }
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(["error" => "Database error: " . $e->getMessage()]);
    }


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
 <script>
document.addEventListener("DOMContentLoaded", () => {
    const userProfileContainer = document.getElementById("user-profile");

    const fetchUser = async () => {
        const userId = localStorage.getItem("user_id");
        if (!userId) {
            console.error("No user_id found in localStorage.");
            return;
        }

        try {
            const response = await fetch(`http://localhost/source_code/get_user.php?user_id=${userId}`);
            const data = await response.json();

            if (data.success && data.user) {
                const user = data.user;
                userProfileContainer.innerHTML = `
                    <div class="profile-container">
                        <img src="http://localhost/source_code/${user.profile_image}" alt="Profile Image" class="profile-img">
                        <h2>${user.firstname || "No Name"} ${user.lastname || "No Name"}</h2>
                        <p>Email: ${user.email || "No Email"}</p>
                        <p>Phone: ${user.phone || "No Number"}</p>
                        <a href="/edit.html">Edit Profile</a>
                        <button onclick="handleLogout()">Logout</button>
                    </div>
                `;
            } else {
                console.error("Error: Invalid user data structure.", data);
                userProfileContainer.innerHTML = `<p>User data not found.</p>`;
            }
        } catch (error) {
            console.error("Error fetching user:", error);
        }
    };

    fetchUser();
});

// Logout function
function handleLogout() {
    localStorage.removeItem("user_id");
    window.location.href = "/login.html";
}

</script>