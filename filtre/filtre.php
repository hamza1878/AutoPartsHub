<?php
$host = 'localhost';
$port = 3400;
$dbname = 'express';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;port=$port;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo json_encode(["error" => "Database connection failed: " . $e->getMessage()]);
    exit();
}
header("Access-Control-Allow-Origin: http://localhost:5173");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Content-Type: application/json");
function getFilteredParts($filters) {
    global $pdo;

    $query = "SELECT * FROM car_parts WHERE 1=1";
    
    if (!empty($filters['name'])) {
        $query .= " AND name LIKE :name";
    }
    if (!empty($filters['category'])) {
        $query .= " AND category LIKE :category";
    }
    if (!empty($filters['brand'])) {
        $query .= " AND brand LIKE :brand";
    }
    if (!empty($filters['model'])) {
        $query .= " AND model LIKE :model";
    }
    if (!empty($filters['year'])) {
        $query .= " AND year = :year";
    }
    if (!empty($filters['price'])) {
        $query .= " AND price <= :price";
    }
    if (!empty($filters['condition'])) {
        $query .= " AND `condition` = :condition";
    }
    if (!empty($filters['availability'])) {
        $query .= " AND availability LIKE :availability";
    }
    if (!empty($filters['location'])) {
        $query .= " AND location LIKE :location";
    }
    if (!empty($filters['engine_type'])) {
        $query .= " AND engine_type LIKE :engine_type";
    }
    if (!empty($filters['transmission_type'])) {
        $query .= " AND transmission_type LIKE :transmission_type";
    }
    if (!empty($filters['brake_type'])) {
        $query .= " AND brake_type LIKE :brake_type";
    }
    if (!empty($filters['suspension_type'])) {
        $query .= " AND suspension_type LIKE :suspension_type";
    }
    if (!empty($filters['exhaust_type'])) {
        $query .= " AND exhaust_type LIKE :exhaust_type";
    }

    $stmt = $pdo->prepare($query);

    if (!empty($filters['name'])) {
        $stmt->bindValue(':name', '%' . $filters['name'] . '%');
    }
    if (!empty($filters['category'])) {
        $stmt->bindValue(':category', '%' . $filters['category'] . '%');
    }
    if (!empty($filters['brand'])) {
        $stmt->bindValue(':brand', '%' . $filters['brand'] . '%');
    }
    if (!empty($filters['model'])) {
        $stmt->bindValue(':model', '%' . $filters['model'] . '%');
    }
    if (!empty($filters['year'])) {
        $stmt->bindValue(':year', $filters['year']);
    }
    if (!empty($filters['price'])) {
        $stmt->bindValue(':price', $filters['price']);
    }
    if (!empty($filters['condition'])) {
        $stmt->bindValue(':condition', $filters['condition']);
    }
    if (!empty($filters['availability'])) {
        $stmt->bindValue(':availability', '%' . $filters['availability'] . '%');
    }
    if (!empty($filters['location'])) {
        $stmt->bindValue(':location', '%' . $filters['location'] . '%');
    }
    if (!empty($filters['engine_type'])) {
        $stmt->bindValue(':engine_type', '%' . $filters['engine_type'] . '%');
    }
    if (!empty($filters['transmission_type'])) {
        $stmt->bindValue(':transmission_type', '%' . $filters['transmission_type'] . '%');
    }
    if (!empty($filters['brake_type'])) {
        $stmt->bindValue(':brake_type', '%' . $filters['brake_type'] . '%');
    }
    if (!empty($filters['suspension_type'])) {
        $stmt->bindValue(':suspension_type', '%' . $filters['suspension_type'] . '%');
    }
    if (!empty($filters['exhaust_type'])) {
        $stmt->bindValue(':exhaust_type', '%' . $filters['exhaust_type'] . '%');
    }

    $stmt->execute();

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

$filters = json_decode(file_get_contents('php://input'), true);

$filteredParts = getFilteredParts($filters);

echo json_encode($filteredParts);
?>
