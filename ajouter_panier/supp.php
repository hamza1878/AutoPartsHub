<?php
session_start();

if (isset($_GET['index'])) {
    $index = $_GET['index'];
    unset($_SESSION['panier'][$index]);
    $_SESSION['panier'] = array_values($_SESSION['panier']); 
}

header('Location: voir_panier.php');
exit;
?>
