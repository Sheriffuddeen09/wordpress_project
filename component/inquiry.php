<?php

$successMessage = "";
$errors = [];

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
    $remark = sanitize_textarea_field($_POST['remark'] ?? '');

    // Validate required fields
    if (!$firstname || !$lastname || !$email || !$phone || !$remark) {
        $errors['server'] = "All fields are required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['server'] = "Invalid email format.";
    } else {
        // Use mysqli instead of WordPress $wpdb
        global $conn;
        $table_name = "inquiry"; // Change to actual table name

        $stmt = $conn->prepare("INSERT INTO $table_name (firstname, lastname, email, phone, remark, created_at) VALUES (?, ?, ?, ?, ?, NOW())");

        if ($stmt) {
            $stmt->bind_param("sssss", $firstname, $lastname, $email, $phone, $remark);
            $insert = $stmt->execute();

            if ($insert) {
                // Email notification
                $to = "your-email@example.com"; // Change this to your actual email
                $subject = "New Inquiry Submission";
                $headers = "From: $email\r\n";
                $headers .= "Reply-To: $email\r\n";
                $headers .= "Content-Type: text/html\r\n";

                $emailMessage = "<h2>New Inquiry Received</h2>
                                 <p><strong>First Name:</strong> $firstname</p>
                                 <p><strong>Last Name:</strong> $lastname</p>
                                 <p><strong>Email:</strong> $email</p>
                                 <p><strong>Phone:</strong> $phone</p>
                                 <p><strong>Remark:</strong> $remark</p>";

                if (wp_mail($to, $subject, $emailMessage, $headers)) {
                    $successMessage = "Inquiry submitted successfully!";
                    $_POST = []; // Clear form fields after success
                } else {
                    $errors['server'] = "Failed to send email notification.";
                }
            } else {
                $errors['server'] = "Database error: " . $stmt->error;
            }
            $stmt->close();
        } else {
            $errors['server'] = "Database error: " . $conn->error;
        }
    }
}
?>

<!-- Inquiry Form -->
<div class="min-h-screen bg-image flex flex-col items-end justify-end - mb-0 p-6" style="background-image: url('<?php echo esc_url(site_url('/component/image/pexels-renya-sh-3464464-11312080\ 1.png')); ?>');">
    <h1 class="text-xl text-center text-white font-bold sm:-translate-x-32">We're Here to Assist You!</h1>
    <div class="bg-pink-300 flex px-2 flex-row sm:w-96 w-72 my-10 p-1 justify-between">
        <h1 class="font-bold text-sm">Submit a request</h1>
        <button class="p-1 w-24 text-white text-sm bg-pink-800">Search</button>
    </div>
    <div class="p-8 rounded-lg shadow-lg w-full max-w-lg bg-pink-300">
        <!-- Success or Error Message -->
        <?php if (!empty($successMessage)) : ?>
            <p class="text-center text-green-500 text-xl font-bold mb-5"><?php echo $successMessage; ?></p>
        <?php elseif (!empty($errors['server'])) : ?>
            <p class="text-center text-red-500 text-xl font-bold mb-5"><?php echo $errors['server']; ?></p>
        <?php endif; ?>

        <form method="POST" class="space-y-4">
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="font-bold mb-4 text-sm">Firstname</label>
                    <input type="text" name="firstname" placeholder="First Name *" class="p-2 mt-2 w-full border rounded text-sm"  required value="<?php echo $_POST['firstname'] ?? ''; ?>">
                </div>
                <div>
                    <label class="font-bold mb-4 text-sm">Lastname</label>
                    <input type="text" name="lastname" placeholder="Last Name *" class="text-sm p-2 w-full border mt-2 rounded" required value="<?php echo $_POST['lastname'] ?? ''; ?>">
                </div>
            </div>
            <div>
                <label class="font-bold mb-4 text-sm">Email</label>
                <input type="email" name="email" placeholder="Email Address *" class="text-sm p-2 w-full border mt-2 rounded" required value="<?php echo $_POST['email'] ?? ''; ?>">
            </div>
            <div>
                <label class="font-bold mb-4 text-sm">Phone</label>
                <input type="text" name="phone" placeholder="Phone Number *" class="text-sm p-2 w-full border mt-2 rounded" required value="<?php echo $_POST['phone'] ?? ''; ?>">
            </div>
            <div>
                <label class="font-bold mb-4 text-sm">Remark</label>
                <textarea name="remark" placeholder="Remark *" class="text-sm p-2 w-full border rounded h-24 mt-2" required><?php echo $_POST['remark'] ?? ''; ?></textarea>
            </div>
            <button type="submit" class="bg-pink-500 text-white py-2 text-sm px-4 w-full rounded hover:bg-pink-600 transition">
                Submit
            </button>
        </form>
    </div>
</div>
