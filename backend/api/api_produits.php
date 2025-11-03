<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

require_once __DIR__ . '/../config/database.php';

try {
    $db = obtenirConnexionBD();
    
    if (isset($_GET['id'])) {
        $stmt = $db->prepare("
            SELECT p.*, c.nom_categorie 
            FROM produits p 
            LEFT JOIN categories c ON p.id_categorie = c.id_categorie 
            WHERE p.id_produit = :id
        ");
        $stmt->execute(['id' => $_GET['id']]);
        $produit = $stmt->fetch();
        
        echo json_encode([
            'success' => true,
            'produit' => $produit
        ]);
    } elseif (isset($_GET['ids'])) {
        // Charger plusieurs produits par leurs IDs
        $ids = explode(',', $_GET['ids']);
        $placeholders = str_repeat('?,', count($ids) - 1) . '?';
        $stmt = $db->prepare("
            SELECT p.*, c.nom_categorie 
            FROM produits p 
            LEFT JOIN categories c ON p.id_categorie = c.id_categorie 
            WHERE p.id_produit IN ($placeholders)
        ");
        $stmt->execute($ids);
        $produits = $stmt->fetchAll();
        
        // Réorganiser les produits dans l'ordre des IDs fournis
        $produitsOrdonnes = [];
        foreach ($ids as $id) {
            foreach ($produits as $produit) {
                if ($produit['id_produit'] == $id) {
                    $produitsOrdonnes[] = $produit;
                    break;
                }
            }
        }
        
        echo json_encode([
            'success' => true,
            'produits' => $produitsOrdonnes
        ]);
    } elseif (isset($_GET['vedettes'])) {
        $stmt = $db->query("
            SELECT p.*, c.nom_categorie 
            FROM produits p 
            LEFT JOIN categories c ON p.id_categorie = c.id_categorie 
            WHERE p.est_vedette = 1
            ORDER BY p.date_ajout_produit DESC
        ");
        $produits = $stmt->fetchAll();
        
        echo json_encode([
            'success' => true,
            'produits' => $produits
        ]);
    } elseif (isset($_GET['categorie'])) {
        $stmt = $db->prepare("
            SELECT p.*, c.nom_categorie 
            FROM produits p 
            LEFT JOIN categories c ON p.id_categorie = c.id_categorie 
            WHERE p.id_categorie = :categorie
            ORDER BY p.nom_produit
        ");
        $stmt->execute(['categorie' => $_GET['categorie']]);
        $produits = $stmt->fetchAll();
        
        echo json_encode([
            'success' => true,
            'produits' => $produits
        ]);
    } else {
        $stmt = $db->query("
            SELECT p.*, c.nom_categorie 
            FROM produits p 
            LEFT JOIN categories c ON p.id_categorie = c.id_categorie 
            ORDER BY p.nom_produit
        ");
        $produits = $stmt->fetchAll();
        
        echo json_encode([
            'success' => true,
            'produits' => $produits
        ]);
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Erreur serveur: ' . $e->getMessage()
    ]);
}
?>
