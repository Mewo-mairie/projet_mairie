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

// Démarrer la session
session_start();

// Headers pour l'API REST
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

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
try {
    // Détruire toutes les variables de session
    $_SESSION = array();
    
    // Détruire le cookie de session si existant
    if (isset($_COOKIE[session_name()])) {
        setcookie(session_name(), '', time() - 3600, '/');
    }
    
    // Détruire la session
    session_destroy();
    
    http_response_code(200); // OK
    envoyerReponseJSON(true, "Déconnexion réussie");
} catch (Exception $e) {
    http_response_code(500); // Internal Server Error
    envoyerReponseJSON(false, "Erreur lors de la déconnexion: " . $e->getMessage());
}
