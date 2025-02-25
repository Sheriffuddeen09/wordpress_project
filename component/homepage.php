<?php

require_once 'database.php'; // Ensure database connection is included

$allowedOrigins = ['http://localhost:3000', 'http://localhost:3001', 'http://another-allowed-origin.com'];

error_reporting(E_ALL);
ini_set('display_errors', 1);

if (isset($_SERVER['HTTP_ORIGIN']) && in_array($_SERVER['HTTP_ORIGIN'], $allowedOrigins)) {
    header("Access-Control-Allow-Origin: " . $_SERVER['HTTP_ORIGIN']);
    header("Access-Control-Allow-Credentials: true");
    header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
    header("Access-Control-Allow-Headers: Content-Type, Authorization");
    header("Access-Control-Max-Age: 86400"); 
}

$limit = isset($_GET['limit']) ? intval($_GET['limit']) : 4;
$pages = isset($_GET['pages']) ? intval($_GET['pages']) : 1;
$offset = ($pages - 1) * $limit;
$location = isset($_GET['location']) ? trim($_GET['location']) : '';
$dateSearch = isset($_GET['dateSearch']) ? trim($_GET['dateSearch']) : '';
$dropSearch = isset($_GET['dropSearch']) ? trim($_GET['dropSearch']) : '';

try {
    global $pdo;    

    $query = "SELECT * FROM homeproduct";
    $conditions = [];

    if (!empty($location)) {
        $conditions[] = "location LIKE :location";
    }
    if (!empty($dateSearch)) {
        $conditions[] = "DATE(pick_up_date) = :dateSearch";
    }
    if (!empty($dropSearch)) {
        $conditions[] = "DATE(drop_off_date) = :dropSearch";
    }

    // Add WHERE clause if there are conditions
    if (count($conditions) > 0) {
        $query .= " WHERE " . implode(" AND ", $conditions);
    }

    $query .= " LIMIT :limit OFFSET :offset";

    $result = $pdo->prepare($query);
    
    if (!empty($location)) {
        $result->bindValue(':location', "%$location%", PDO::PARAM_STR);
    }
    if (!empty($dateSearch)) {
        $result->bindValue(':dateSearch', $dateSearch, PDO::PARAM_STR);
    }
    if (!empty($dropSearch)) {
        $result->bindValue(':dropSearch', $dropSearch, PDO::PARAM_STR);
    }
    
    $result->bindValue(':limit', $limit, PDO::PARAM_INT);
    $result->bindValue(':offset', $offset, PDO::PARAM_INT);

    $result->execute();

    $pets = $result->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode(["success" => true, "message" => $pets]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(["error" => "Database error: " . $e->getMessage()]);
}
?>
