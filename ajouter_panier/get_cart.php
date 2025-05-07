<?php
$host = 'localhost';
$port = 3400;
$dbname = 'express';
$username = 'root';
$password = '';

$pdo = new PDO("mysql:host=$host;port=$port;dbname=$dbname", $username, $password);
header("Access-Control-Allow-Origin: http://localhost:5173");
header("Access-Control-Allow-Methods: GET, OPTIONS");
header("Content-Type: application/json");

$userId = $_GET['userId'] ?? 0;

$stmt = $pdo->prepare("
    SELECT cp.id, cp.name, cp.price, cp.image, cp.model, c.quantity
    FROM carts c
    JOIN car_parts cp ON c.product_id = cp.id
    WHERE c.user_id = ?
");
$stmt->execute([$userId]);

echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
