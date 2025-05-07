<?php
header("Access-Control-Allow-Origin: http://localhost:5173");
header("Access-Control-Allow-Methods: GET, OPTIONS");
header("Content-Type: application/json");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

class Database {
    private $host = 'localhost';
    private $port = 3400;
    private $dbname = 'express';
    private $username = 'root';
    private $password = '';
    private $conn;

    public function __construct() {
        try {
            $this->conn = new PDO("mysql:host=$this->host;port=$this->port;dbname=$this->dbname", $this->username, $this->password, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Database connection failed: ' . $e->getMessage()]);
            exit;
        }
    }

    public function getConnection() {
        return $this->conn;
    }
}

class CarManager {
    private $db;
    private $uploadDir = 'uploads/';
    private $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
    private $maxFileSize = 5 * 1024 * 1024; // 5MB

    public function __construct(Database $db) {
        $this->db = $db->getConnection();
        if (!is_dir($this->uploadDir)) {
            mkdir($this->uploadDir, 0755, true);
        }
    }

    public function addCar($formData, $files) {
        $response = ['success' => false, 'message' => '', 'files' => []];

        $requiredFields = ['category', 'brand'];
        foreach ($requiredFields as $field) {
            if (empty($formData[$field])) {
                $response['message'] = "Le champ $field est requis.";
                return $response;
            }
        }

        $carData = [
            'category' => $formData['category'] ?? '',
            'brand' => $formData['brand'] ?? '',
            'model' => $formData['model'] ?? '',
            'custom_model' => $formData['custom_model'] ?? '',
            'year' => !empty($formData['year']) ? (int)$formData['year'] : null,
            'price' => !empty($formData['price']) ? (float)$formData['price'] : null,
            'condition' => $formData['condition'] ?? '',
            'availability' => $formData['availability'] ?? '',
            'location' => $formData['location'] ?? '',
            'engine_type' => $formData['engine_type'] ?? '',
            'transmission_type' => $formData['transmission_type'] ?? '',
            'price_range' => $formData['price_range'] ?? ''
        ];

        try {
            $this->db->beginTransaction();

            // Insertion des données de la voiture
            $stmt = $this->db->prepare("INSERT INTO carspices (category, brand, model, custom_model, year, price, `condition`, availability, location, engine_type, transmission_type, price_range) VALUES (:category, :brand, :model, :custom_model, :year, :price, :condition, :availability, :location, :engine_type, :transmission_type, :price_range)");
            $stmt->execute($carData);
            $carId = $this->db->lastInsertId();

            // Gestion des images
            if (!empty($files['images'])) {
                foreach ($files['images']['name'] as $key => $name) {
                    if ($files['images']['error'][$key] === UPLOAD_ERR_OK) {
                        $fileType = $files['images']['type'][$key];
                        $fileSize = $files['images']['size'][$key];
                        $tmpName = $files['images']['tmp_name'][$key];

                        if (!in_array($fileType, $this->allowedTypes)) {
                            throw new Exception("Type de fichier non autorisé pour $name.");
                        }
                        if ($fileSize > $this->maxFileSize) {
                            throw new Exception("Fichier $name trop volumineux (max 5MB).");
                        }

                        $fileExt = pathinfo($name, PATHINFO_EXTENSION);
                        $fileName = uniqid('car_' . $carId . '_') . '.' . $fileExt;
                        $filePath = $this->uploadDir . $fileName;

                        if (!move_uploaded_file($tmpName, $filePath)) {
                            throw new Exception("Échec de l'upload de $name.");
                        }

                        // Enregistrement du chemin de l'image dans la base de données
                        $stmt = $this->db->prepare("INSERT INTO car_images (car_id, image_path) VALUES (:car_id, :image_path)");
                        $stmt->execute(['car_id' => $carId, 'image_path' => $filePath]);
                        $response['files'][] = $filePath;
                    }
                }
            }

            $this->db->commit();
            $response['success'] = true;
            $response['message'] = 'Voiture ajoutée avec succès.';
        } catch (Exception $e) {
            $this->db->rollBack();
            $response['message'] = 'Erreur: ' . $e->getMessage();
        }

        return $response;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $db = new Database();
    $carManager = new CarManager($db);
    $response = $carManager->addCar($_POST, $_FILES);
    echo json_encode($response);
} else {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Méthode non autorisée.']);
}
?>
