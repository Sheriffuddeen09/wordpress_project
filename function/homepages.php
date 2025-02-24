<?php

require_once '../database/function.php';

header("Content-Type: application/json");

$limit = isset($_GET['limit']) ? intval($_GET['limit']) : 4;
$pages = isset($_GET['pages']) ? intval($_GET['pages']) : 1;
$offset = ($pages - 1) * $limit;
$location = isset($_GET['location']) ? trim($_GET['location']) : '';
$dateSearch = isset($_GET['dateSearch']) ? trim($_GET['dateSearch']) : '';
$dropSearch = isset($_GET['dropSearch']) ? trim($_GET['dropSearch']) : '';
$viewAll = isset($_GET['viewAll']) ? true : false;

try {
    global $conn;

    $query = "SELECT * FROM homeproducts";
    $conditions = [];
    $params = [];
    $types = ""; // MySQLi requires explicit type definitions

    // Apply filters if provided
    if (!empty($location)) {
        $conditions[] = "location LIKE ?";
        $params[] = "%$location%";
        $types .= "s"; // String type
    }
    if (!empty($dateSearch)) {
        $conditions[] = "DATE(pick_up_date) = ?";
        $params[] = $dateSearch;
        $types .= "s";
    }
    if (!empty($dropSearch)) {
        $conditions[] = "DATE(drop_off_date) = ?";
        $params[] = $dropSearch;
        $types .= "s";
    }

    // Add WHERE clause if conditions exist
    if (!empty($conditions)) {
        $query .= " WHERE " . implode(" AND ", $conditions);
    }

    // Apply pagination only if "View All" is NOT selected
    if (!$viewAll) {
        $query .= " LIMIT ? OFFSET ?";
        $params[] = $limit;
        $params[] = $offset;
        $types .= "ii"; // Integers for limit and offset
    }

    // Prepare SQL statement
    $stmt = $conn->prepare($query);
    if (!$stmt) {
        throw new Exception("Query preparation failed: " . $conn->error);
    }

    // Bind parameters dynamically
    if (!empty($params)) {
        $stmt->bind_param($types, ...$params);
    }

    $stmt->execute();
    $result = $stmt->get_result();
    $pets = $result->fetch_all(MYSQLI_ASSOC);

    echo json_encode(["success" => true, "message" => $pets]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(["error" => "Database error: " . $e->getMessage()]);
}
?>
