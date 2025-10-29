<?php
// API pour l'inscription des utilisateurs

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
    if (empty($donnees['email_utilisateur']) || 
        empty($donnees['mot_de_passe']) || 
        empty($donnees['prenom_utilisateur']) || 
        empty($donnees['nom_utilisateur'])) {
        echo json_encode(['success' => false, 'message' => 'Tous les champs sont requis']);
        exit;
    }
    
    // Vérifier si l'email existe déjà
    if ($modele_utilisateur->emailExiste($donnees['email_utilisateur'])) {
        echo json_encode(['success' => false, 'message' => 'Cet email est déjà utilisé']);
        exit;
    }
    
    // Créer l'utilisateur
    $id = $modele_utilisateur->creerUtilisateur(
        $donnees['email_utilisateur'],
        $donnees['mot_de_passe'],
        $donnees['prenom_utilisateur'],
        $donnees['nom_utilisateur']
    );
    
    echo json_encode([
        'success' => true, 
        'message' => 'Inscription réussie',
        'id_utilisateur' => $id
    ]);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Erreur serveur: ' . $e->getMessage()]);
}
