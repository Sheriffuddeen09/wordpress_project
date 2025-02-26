<?php  
require_once dirname(__DIR__) . '/wp-load.php';
require_once '../database/function.php';

header('Content-Type: application/json');
session_start();

// Set session lifetime to 2 days (172800 seconds)
$session_lifetime = 172800;
ini_set('session.gc_maxlifetime', $session_lifetime);
session_set_cookie_params($session_lifetime);

// Check if session is expired
if (isset($_SESSION['login_time']) && (time() - $_SESSION['login_time'] > $session_lifetime)) {
    session_unset();
    session_destroy();
    echo json_encode(["status" => "error", "message" => "Session expired, please log in again"]);
    exit();
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Check if database connection is available
    if (!isset($conn)) {
        echo json_encode(["status" => "error", "message" => "Database connection failed"]);
        exit();
    }

    // Sanitize input
    $email = trim($_POST["email"]);
    $password = trim($_POST["password"]);

    if (empty($email) || empty($password)) {
        echo json_encode(["status" => "error", "message" => "Email and password are required"]);
        exit();
    }

    // Fetch user
    $stmt = $conn->prepare("SELECT ID, firstname, lastname, email, password, profile_image FROM users WHERE email = ?");
    if (!$stmt) {
        echo json_encode(["status" => "error", "message" => "Database error"]);
        exit();
    }
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        echo json_encode(["status" => "error", "message" => "Email not registered"]);
        exit();
    }

    $user = $result->fetch_assoc();

    // Verify password
    if (!password_verify($password, $user["password"])) {
        echo json_encode(["status" => "error", "message" => "Invalid password"]);
        exit();
    }

    // Store session data
    $_SESSION["user_id"] = $user["ID"];
    $_SESSION["user_name"] = $user["firstname"] . " " . $user["lastname"];
    $_SESSION["user_email"] = $user["email"];
    $_SESSION["user_profile_image"] = $user["profile_image"];
    $_SESSION['login_time'] = time();

    // Return response
    echo json_encode([
        "status" => "success",
        "redirect" => "dashboard/profile.html",
        "user" => [
            "id" => $user["ID"],
            "firstname" => $user["firstname"],
            "lastname" => $user["lastname"],
            "email" => $user["email"],
            "profile_image" => $user["profile_image"]
        ]
    ]);
    exit();
} else {
    echo json_encode(["status" => "error", "message" => "Invalid request"]);
    exit();
}
?>
