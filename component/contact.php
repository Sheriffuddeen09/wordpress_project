<?php
header("Content-Type: application/json"); // Ensure JSON response
header("Access-Control-Allow-Origin: *"); // Allow cross-origin if needed

// Initialize message variable
$message = "";

// Process the form submission if it is a POST request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Load WordPress functions
    require_once './wp-load.php';
    require_once './database/function.php';

    // Sanitize and validate input data
    $firstname = sanitize_text_field($_POST['firstname'] ?? '');
    $lastname = sanitize_text_field($_POST['lastname'] ?? '');
    $email = sanitize_email($_POST['email'] ?? '');
    $phone = sanitize_text_field($_POST['phone'] ?? '');
    $messageInput = sanitize_textarea_field($_POST['message'] ?? '');

    // Validate required fields
    if (!$firstname || !$lastname || !$email || !$phone || !$messageInput) {
        $message = "All fields are required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = "Invalid email format.";
    } else {
        // Use mysqli instead of WordPress $wpdb
        global $conn;
        $table_name = "contact"; // Change to actual table name

        $stmt = $conn->prepare("INSERT INTO $table_name (firstname, lastname, email, phone, message, created_at) VALUES (?, ?, ?, ?, ?, NOW())");

        if ($stmt) {
            $stmt->bind_param("sssss", $firstname, $lastname, $email, $phone, $messageInput);
            $insert = $stmt->execute();

            if ($insert) {
                // Email notification
                $to = "your-email@example.com"; // Change this to your actual email
                $subject = "New Contact Form Submission";
                $headers = "From: $email\r\n";
                $headers .= "Reply-To: $email\r\n";
                $headers .= "Content-Type: text/html\r\n";

                $emailMessage = "<h2>New Inquiry Received</h2>
                                 <p><strong>First Name:</strong> $firstname</p>
                                 <p><strong>Last Name:</strong> $lastname</p>
                                 <p><strong>Email:</strong> $email</p>
                                 <p><strong>Phone:</strong> $phone</p>
                                 <p><strong>Message:</strong> $messageInput</p>";

                if (wp_mail($to, $subject, $emailMessage, $headers)) {
                    $message = "Message sent successfully!";
                    $_POST = []; // Clear form fields after success
                } else {
                    $message = "Failed to send email notification.";
                }
            } else {
                $message = "Database error: " . $stmt->error;
            }
            $stmt->close();
        } else {
            $message = "Database error: " . $conn->error;
        }
    }
}
?>

<!-- Contact Form -->
<body>
<img src="<?php echo esc_url(site_url('/component/image/Frame 129.png')); ?>" alt="imagepicture" class="w-full -mt-2 -translate-y-10 rounded-none" />
<div class="min-h-screen bg-contact flex flex-col  -mt-8 items-center justify-center p-6" style="background-image: url('<?php echo esc_url(site_url('/layout/image/background.png')); ?>');">
    <h1 class="text-black -mt-4 mb-2">Contact Customer Support</h1>

    <div class="flex gap-2 flex-wrap mx-auto text-sm my-6">
        <div class="inline-flex gap-2 text-sm">
            <img src="<?php echo esc_url(site_url('/component/image/Vector (1).png')); ?>" alt="logo" class="bg-[#d9286c] w-8 p-2 h-8 rounded-full bg-pink-700"/>
            <div>
                <p style="font-size:10px" class="font-bold">Address</p>
                <p class="w-20 font-bold text-center" style="font-size:8px">Sentral DTLA at 755 S. Spring</p>
            </div>
        </div>
        <div class="inline-flex gap-2 text-sm">
            <img src="<?php echo esc_url(site_url('/component/image/Vector (2).png')); ?>" alt="logo" class="bg-[#d9286c] w-8 p-2 h-8 rounded-full bg-pink-700"/>
            <div>
                <p style="font-size:10px" class="font-bold">Phone</p>
                <p class="w-20 font-bold" style="font-size:8px">+(123) 654 6540</p>
            </div>
        </div>
        <div class="inline-flex gap-2 text-sm">
            <img src="<?php echo esc_url(site_url('/component/image/Vector.png')); ?>" alt="logo" class="bg-[#d9286c] w-8 p-2 h-8 rounded-full bg-pink-700"/>
            <div>
                <p style="font-size:10px" class="font-bold">Email</p>
                <p class="w-20 font-bold" style="font-size:8px">petrental@123.com</p>
            </div>
        </div>
    </div>

    <div class="p-8 rounded-lg w-full max-w-lg">
        <!-- Success or Error Message -->
        <?php if (!empty($message)) : ?>
            <p class="text-center text-<?php echo ($message === "Message sent successfully!") ? 'green' : 'red'; ?>-500 text-xl font-bold mb-5"><?php echo $message; ?></p>
        <?php endif; ?>

        <form method="POST" class="space-y-4">
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="font-bold mb-4 text-sm">Firstname</label>
                    <input type="text" name="firstname" placeholder="First Name *" class="p-2 text-sm w-full mt-3 border rounded" required value="<?php echo isset($_POST['firstname']) ? esc_attr($_POST['firstname']) : ''; ?>" />
                </div>
                <div>
                    <label class="font-bold mb-4 text-sm">Lastname</label>
                    <input type="text" name="lastname" placeholder="Last Name *" class="p-2 text-sm w-full border mt-3 rounded" required value="<?php echo isset($_POST['lastname']) ? esc_attr($_POST['lastname']) : ''; ?>" />
                </div>
            </div>
            <div>
                <label class="font-bold mb-4 text-sm">Email</label>
                <input type="email" name="email" placeholder="Email Address *" class="p-2 text-sm w-full border mt-2 rounded" required value="<?php echo isset($_POST['email']) ? esc_attr($_POST['email']) : ''; ?>" />
            </div>
            <div>
                <label class="font-bold mb-4 text-sm">Phone</label>
                <input type="text" name="phone" placeholder="Phone Number *" class="p-2 text-sm w-full border mt-2 rounded" required value="<?php echo isset($_POST['phone']) ? esc_attr($_POST['phone']) : ''; ?>" />
            </div>
            <div>
                <label class="font-bold mb-4 text-sm">Message</label>
                <textarea name="message" placeholder="Message *" class="p-2 text-sm w-full border rounded mt-2 h-24" required><?php echo isset($_POST['message']) ? esc_textarea($_POST['message']) : ''; ?></textarea>
            </div>
            <button type="submit" class="bg-pink-500 text-sm text-white py-2 px-4 w-full rounded hover:bg-pink-600 transition">
                Submit
            </button>
        </form>
    </div>
</div>
<div><div class="w-full max-w-lg mx-auto mt-6">
    <div class="w-full h-64">
        <iframe 
            width="100%" 
            height="100%" 
            style="border:0;" 
            loading="lazy" 
            allowfullscreen 
            referrerpolicy="no-referrer-when-downgrade"
            src="https://www.google.com/maps/embed/v1/place?key=YOUR_GOOGLE_MAPS_API_KEY&q=Sentral+DTLA+at+755+S.+Spring">
        </iframe>
    </div>
</div>
</div>
</body>
