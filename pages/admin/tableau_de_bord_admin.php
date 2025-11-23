<?php
/**
 * Page du tableau de bord administrateur
 * Redirige vers la gestion des produits
 */

// Démarrer la session
session_start();

// Vérifier que l'utilisateur est connecté et est admin
if (!isset($_SESSION['utilisateur_connecte']) || $_SESSION['role_utilisateur'] !== 'administrateur') {
    header('Location: ../connexion.html');
    exit;
}

// Rediriger vers la gestion des produits
header('Location: gestion_produits.php');
exit;
?>
