<?php
// API pour gérer les produits

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
header('Access-Control-Allow-Headers: Content-Type');

require_once __DIR__ . '/../models/modele_produit.php';

$modele_produit = new ModeleProduit();
$methode = $_SERVER['REQUEST_METHOD'];

try {
    switch ($methode) {
        case 'GET':
            if (isset($_GET['id'])) {
                // Récupérer un produit par ID
                $produit = $modele_produit->obtenirProduitParId($_GET['id']);
                echo json_encode(['success' => true, 'produit' => $produit]);
            } elseif (isset($_GET['categorie'])) {
                // Récupérer les produits par catégorie
                $produits = $modele_produit->obtenirProduitsParCategorie($_GET['categorie']);
                echo json_encode(['success' => true, 'produits' => $produits]);
            } elseif (isset($_GET['vedettes'])) {
                // Récupérer les produits vedettes
                $produits = $modele_produit->obtenirProduitsVedettes();
                echo json_encode(['success' => true, 'produits' => $produits]);
            } else {
                // Récupérer tous les produits
                $produits = $modele_produit->obtenirTousLesProduits();
                echo json_encode(['success' => true, 'produits' => $produits]);
            }
            break;
            
        case 'POST':
            // Créer un nouveau produit
            $donnees = json_decode(file_get_contents('php://input'), true);
            
            $id = $modele_produit->creerProduit(
                $donnees['nom_produit'],
                $donnees['description_produit'] ?? '',
                $donnees['id_categorie'],
                $donnees['image_url_produit'] ?? '',
                $donnees['est_vedette'] ?? 0
            );
            
            echo json_encode(['success' => true, 'message' => 'Produit créé', 'id' => $id]);
            break;
            
        case 'PUT':
            // Modifier un produit
            $donnees = json_decode(file_get_contents('php://input'), true);
            
            $resultat = $modele_produit->modifierProduit(
                $donnees['id_produit'],
                $donnees['nom_produit'],
                $donnees['description_produit'] ?? '',
                $donnees['id_categorie'],
                $donnees['image_url_produit'] ?? '',
                $donnees['est_vedette'] ?? 0
            );
            
            echo json_encode(['success' => $resultat, 'message' => 'Produit modifié']);
            break;
            
        case 'DELETE':
            // Supprimer un produit
            $donnees = json_decode(file_get_contents('php://input'), true);
            
            $resultat = $modele_produit->supprimerProduit($donnees['id_produit']);
            
            echo json_encode(['success' => $resultat, 'message' => 'Produit supprimé']);
            break;
            
        default:
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Méthode non autorisée']);
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Erreur serveur: ' . $e->getMessage()]);
}
