<?php
// API pour gérer les catégories

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
header('Access-Control-Allow-Headers: Content-Type');

require_once __DIR__ . '/../models/modele_categorie.php';

$modele_categorie = new ModeleCategorie();
$methode = $_SERVER['REQUEST_METHOD'];

try {
    switch ($methode) {
        case 'GET':
            if (isset($_GET['id'])) {
                // Récupérer une catégorie par ID
                $categorie = $modele_categorie->obtenirCategorieParId($_GET['id']);
                echo json_encode(['success' => true, 'categorie' => $categorie]);
            } else {
                // Récupérer toutes les catégories
                $categories = $modele_categorie->obtenirToutesLesCategories();
                echo json_encode(['success' => true, 'categories' => $categories]);
            }
            break;
            
        case 'POST':
            // Créer une nouvelle catégorie
            $donnees = json_decode(file_get_contents('php://input'), true);
            
            $id = $modele_categorie->creerCategorie(
                $donnees['nom_categorie'],
                $donnees['description_categorie'] ?? '',
                $donnees['image_url_categorie'] ?? ''
            );
            
            echo json_encode(['success' => true, 'message' => 'Catégorie créée', 'id' => $id]);
            break;
            
        case 'PUT':
            // Modifier une catégorie
            $donnees = json_decode(file_get_contents('php://input'), true);
            
            $resultat = $modele_categorie->modifierCategorie(
                $donnees['id_categorie'],
                $donnees['nom_categorie'],
                $donnees['description_categorie'] ?? '',
                $donnees['image_url_categorie'] ?? ''
            );
            
            echo json_encode(['success' => $resultat, 'message' => 'Catégorie modifiée']);
            break;
            
        case 'DELETE':
            // Supprimer une catégorie
            $donnees = json_decode(file_get_contents('php://input'), true);
            
            $resultat = $modele_categorie->supprimerCategorie($donnees['id_categorie']);
            
            echo json_encode(['success' => $resultat, 'message' => 'Catégorie supprimée']);
            break;
            
        default:
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Méthode non autorisée']);
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Erreur serveur: ' . $e->getMessage()]);
}
