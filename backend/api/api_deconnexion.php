<?php
/**
 * API REST pour la déconnexion des utilisateurs
 * Point d'entrée : POST /backend/api/api_deconnexion.php
 * 
 * Réponse (JSON) :
 * {
 *   "succes": true|false,
 *   "message": "Message d'information"
 * }
 */

// Headers pour l'API REST
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

// Inclure le contrôleur de connexion
require_once __DIR__ . '/../controllers/controleur_connexion.php';

// Fonction pour envoyer une réponse JSON
function envoyerReponseJSON($succes, $message) {
    echo json_encode([
        'succes' => $succes,
        'message' => $message
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

// Vérifier que la méthode est POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405); // Method Not Allowed
    envoyerReponseJSON(false, "Méthode non autorisée. Utilisez POST.");
}

// Déconnecter l'utilisateur
$deconnexion_reussie = deconnecterUtilisateur();

if ($deconnexion_reussie) {
    http_response_code(200); // OK
    envoyerReponseJSON(true, "Déconnexion réussie");
} else {
    http_response_code(500); // Internal Server Error
    envoyerReponseJSON(false, "Erreur lors de la déconnexion");
}
