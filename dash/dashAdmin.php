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

$stmt = $pdo->query("SELECT * FROM dashboard_data LIMIT 1");
$data = $stmt->fetch(PDO::FETCH_ASSOC);

$response = [
  "user" => $data["user_name"],
  "nextRevision" => [
    "label" => "Prochaine révision",
    "icon" => "🔧",
    "date" => date("d F Y à H\h", strtotime($data["next_revision"]))
  ],
  "orderStatus" => [
    "label" => "Batterie commandée",
    "icon" => "🛒",
    "date" => date("d F Y", strtotime($data["order_date"]))
  ],
  "menu" => [ 
    ["icon" => "🏠", "label" => "Tableau de bord", "router" => "/dashboard/DynamicForm",],
    ["icon" => "📅", "label" => "Mes rendez-vous","router" => "/dashboard/CreateOrder"],
    ["icon" => "🛒", "label" => "Mes commandes", "router" => "/dashboard/CreateOrder"],
    ["icon" => "🧾", "label" => "Mes factures", "router" => "/dashboard/MyInvoices"],
    ["icon" => "🛠️", "label" => "AddExpress", "router" => "/dashboard/AddExpress"],
    ["icon" => "👤", "label" => "Mon profil", "router" => "/UserProfile"]
   
  ]
];

echo json_encode($response);
?>
