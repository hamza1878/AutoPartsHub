<?php
header("Access-Control-Allow-Origin: http://localhost:5173");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

include('config.php'); 

$data = json_decode(file_get_contents("php://input"), true);

if (isset($data['email'])) {
    $email = $data['email'];

    $stmt = $pdo->prepare("SELECT id FROM auth WHERE email = ?");
    $stmt->execute([$email]);

    echo json_encode(["exists" => $stmt->fetch() ? true : false]);
} else {
    echo json_encode(["error" => "Email not provided"]);
}
