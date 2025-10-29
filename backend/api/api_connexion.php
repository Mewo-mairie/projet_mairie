<?php
// API pour la connexion des utilisateurs

session_start();

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

require_once __DIR__ . '/../models/modele_utilisateur.php';

$modele_utilisateur = new ModeleUtilisateur();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Méthode non autorisée']);
    exit;
}

try {
    $donnees = json_decode(file_get_contents('php://input'), true);
    
    // Vérifier les champs requis
    if (empty($donnees['email_utilisateur']) || empty($donnees['mot_de_passe'])) {
        echo json_encode(['success' => false, 'message' => 'Email et mot de passe requis']);
        exit;
    }
    
    // Vérifier les identifiants
    $utilisateur = $modele_utilisateur->verifierConnexion(
        $donnees['email_utilisateur'],
        $donnees['mot_de_passe']
    );
    
    if (!$utilisateur) {
        echo json_encode(['success' => false, 'message' => 'Email ou mot de passe incorrect']);
        exit;
    }
    
    // Créer la session
    $_SESSION['utilisateur_id'] = $utilisateur['id_utilisateur'];
    $_SESSION['utilisateur_email'] = $utilisateur['email_utilisateur'];
    $_SESSION['utilisateur_prenom'] = $utilisateur['prenom_utilisateur'];
    $_SESSION['utilisateur_nom'] = $utilisateur['nom_utilisateur'];
    $_SESSION['utilisateur_role'] = $utilisateur['role_utilisateur'];
    
    // Supprimer le mot de passe de la réponse
    unset($utilisateur['mot_de_passe_hash']);
    
    echo json_encode([
        'success' => true, 
        'message' => 'Connexion réussie',
        'utilisateur' => $utilisateur
    ]);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Erreur serveur: ' . $e->getMessage()]);
}
