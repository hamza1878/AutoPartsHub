<?php
$host = 'localhost';
$port = 3400;
$dbname = 'express';
$username = 'root';
$password = '';

$pdo = new PDO("mysql:host=$host;port=$port;dbname=$dbname", $username, $password);
header("Access-Control-Allow-Origin: http://localhost:5173");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Content-Type: application/json");

$data = json_decode(file_get_contents("php://input"), true);
$userId = $data['userId'];
$cart = $data['cart'];

$stmt = $pdo->prepare("DELETE FROM carts WHERE user_id = ?");
$stmt->execute([$userId]);

$stmt = $pdo->prepare("INSERT INTO carts (user_id, product_id, quantity) VALUES (?, ?, ?)");
foreach ($cart as $item) {
    $stmt->execute([$userId, $item['productId'], $item['quantity']]);
}

echo json_encode(["status" => "success"]);
