<?php
header("Access-Control-Allow-Origin: http://localhost:5173");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Content-Type: application/json");

include 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents("php://input"), true);

    $username = trim($data['username'] ?? '');
    $password = trim($data['password'] ?? '');

    if (empty($username) || empty($password)) {
        echo json_encode(["status" => 400, "message" => "Username and password are required."]);
        exit;
    }

    try {
        $stmt = $pdo->prepare("SELECT id, password FROM auth WHERE username = :username");
        $stmt->execute([':username' => $username]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password'])) {
            echo json_encode(["status" => 200, "message" => "Login successful"]);
        } else {
            echo json_encode(["status" => 401, "message" => "Invalid username or password"]);
        }
    } catch (PDOException $e) {
        echo json_encode(["status" => 500, "message" => "Error: " . $e->getMessage()]);
    }
} else {
    echo json_encode(["status" => 405, "message" => "Method not allowed"]);
}
?>
