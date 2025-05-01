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

if (
    isset($data['username']) &&
    isset($data['email']) &&
    isset($data['password']) &&
    isset($data['confirmPassword'])
) {
    $username = $data['username'];
    $email = $data['email'];
    $password = $data['password'];
    $confirmPassword = $data['confirmPassword'];

    if ($password !== $confirmPassword) {
        echo json_encode(["error" => "Passwords do not match"]);
        exit();
    }

    $stmt = $pdo->prepare("SELECT id FROM auth WHERE username = ? OR email = ?");
    $stmt->execute([$username, $email]);

    if ($stmt->fetch()) {
        echo json_encode(["error" => "Username or email already exists"]);
        exit();
    }

    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    $insert = $pdo->prepare("INSERT INTO auth (username, email, password) VALUES (?, ?, ?)");

    if ($insert->execute([$username, $email, $hashedPassword])) {
        echo json_encode(["success" => true, "message" => "User registered successfully!"]);
    } else {
        echo json_encode(["error" => "Failed to register user"]);
    }
} else {
    echo json_encode(["error" => "Missing required fields"]);
}
