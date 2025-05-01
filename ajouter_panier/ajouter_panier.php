<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $piece = [
        'id' => $_POST['id'],
        'nom_piece' => $_POST['nom_piece'],
        'reference' => $_POST['reference'],
        'quantite' => $_POST['quantite']
    ];

    if (!isset($_SESSION['panier'])) {
        $_SESSION['panier'] = [];
    }

    // Ajouter la pièce au panier
    $_SESSION['panier'][] = $piece;

    echo "Pièce ajoutée au panier.<br>";
    echo '<a href="voir_panier.php">Voir le panier</a>';
}
?>
