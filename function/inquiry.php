<?php
require_once dirname(__DIR__) . '/wp-load.php';
require_once '../database/function.php';

header("Content-Type: application/json");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize and validate input
    $firstname = sanitize_text_field($_POST['firstname'] ?? '');
    $lastname = sanitize_text_field($_POST['lastname'] ?? '');
    $email = sanitize_email($_POST['email'] ?? '');
    $phone = sanitize_text_field($_POST['phone'] ?? '');
    $remark = sanitize_textarea_field($_POST['remark'] ?? '');

    if (!$firstname || !$lastname || !$email || !$phone || !$remark) {
        $errors['server'] = "All fields are required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['server'] = "Invalid email format.";
    } else {
        global $conn;
        $table_name = "inquiry"; 

        $stmt = $conn->prepare("INSERT INTO $table_name (firstname, lastname, email, phone, remark, created_at) VALUES (?, ?, ?, ?, ?, NOW())");
        if ($stmt) {
            $stmt->bind_param("sssss", $firstname, $lastname, $email, $phone, $remark);
            $insert = $stmt->execute();

            if ($insert) {
                $to = "your-email@example.com"; 
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
                    echo json_encode(["success" => true, "message" => "Inquiry submitted successfully!"]);
                } else {
                    echo json_encode(["success" => false, "message" => "Failed to send email notification."]);
                }
            } else {
                echo json_encode(["success" => false, "message" => "Database error: " . $stmt->error]);
            }
            $stmt->close();
        } else {
            echo json_encode(["success" => false, "message" => "Database error: " . $conn->error]);
        }
    }
}
?>
