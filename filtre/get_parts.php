<?php
header('Content-Type: application/json');
include('config.php');

try {
    $conditions = [];
    $params = [];

    if (!empty($_GET['category'])) {
        $conditions[] = 'category = ?';
        $params[] = $_GET['category'];
    }

    if (!empty($_GET['brand'])) {
        $conditions[] = 'brand = ?';
        $params[] = $_GET['brand'];
    }

    if (!empty($_GET['model'])) {
        $conditions[] = 'model = ?';
        $params[] = $_GET['model'];
    }

    if (!empty($_GET['year'])) {
        $conditions[] = 'year = ?';
        $params[] = $_GET['year'];
    }

    if (!empty($_GET['condition'])) {
        $conditions[] = 'condition = ?';
        $params[] = $_GET['condition'];
    }

    if (!empty($_GET['availability'])) {
        $conditions[] = 'availability = ?';
        $params[] = $_GET['availability'];
    }

    if (!empty($_GET['location'])) {
        $conditions[] = 'location = ?';
        $params[] = $_GET['location'];
    }

    if (!empty($_GET['min_price'])) {
        $conditions[] = 'price >= ?';
        $params[] = $_GET['min_price'];
    }

    if (!empty($_GET['max_price'])) {
        $conditions[] = 'price <= ?';
        $params[] = $_GET['max_price'];
    }

    $sql = 'SELECT * FROM car_parts';
    if (!empty($conditions)) {
        $sql .= ' WHERE ' . implode(' AND ', $conditions);
    }

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $parts = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($parts);
} catch (PDOException $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
?>
