<?php
/**
 * API REST pour la gestion des utilisateurs (admin uniquement)
 */

// Headers pour l'API REST
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, PUT');
header('Access-Control-Allow-Headers: Content-Type');

// Inclure les fichiers nécessaires
require_once __DIR__ . '/../models/modele_utilisateur.php';
require_once __DIR__ . '/../utils/middleware_authentification.php';

// Fonction pour envoyer une réponse JSON
function envoyerReponseJSON($code_http, $succes, $message, $donnees = null) {
    http_response_code($code_http);
    
    $reponse = [
        'succes' => $succes,
        'message' => $message
    ];
    
    if ($donnees !== null) {
        $reponse['donnees'] = $donnees;
    }
    
    echo json_encode($reponse, JSON_UNESCAPED_UNICODE);
    exit;
}

// Vérifier que l'utilisateur est administrateur
verifierAdminPourAPI();

// Créer une instance du modèle
$modele_utilisateur = new ModeleUtilisateur();

// Récupérer la méthode HTTP
$methode_http = $_SERVER['REQUEST_METHOD'];

// ====================
// GET : Récupérer des utilisateurs
// ====================
if ($methode_http === 'GET') {
    
    // Récupérer un utilisateur spécifique
    if (isset($_GET['id'])) {
        $id_utilisateur = (int)$_GET['id'];
        
        $utilisateur = $modele_utilisateur->obtenirUtilisateurParId($id_utilisateur);
        
        if ($utilisateur) {
            // Ajouter les statistiques
            $stats = $modele_utilisateur->obtenirStatistiquesUtilisateur($id_utilisateur);
            $utilisateur['statistiques'] = $stats;
            
            envoyerReponseJSON(200, true, "Utilisateur récupéré avec succès", $utilisateur);
        } else {
            envoyerReponseJSON(404, false, "Utilisateur non trouvé");
        }
    } else {
        // Récupérer tous les utilisateurs
        $utilisateurs = $modele_utilisateur->obtenirTousLesUtilisateurs();
        
        envoyerReponseJSON(200, true, "Utilisateurs récupérés avec succès", $utilisateurs);
    }
}

// ====================
// PUT : Modifier un utilisateur
// ====================
else if ($methode_http === 'PUT') {
    
    // Récupérer les données JSON
    $donnees_json = file_get_contents('php://input');
    $donnees_recues = json_decode($donnees_json, true);
    
    // Vérifier si les données JSON sont valides
    if ($donnees_recues === null) {
        envoyerReponseJSON(400, false, "Données JSON invalides");
    }
    
    // Vérifier que l'ID est présent
    if (!isset($donnees_recues['id_utilisateur'])) {
        envoyerReponseJSON(400, false, "L'ID de l'utilisateur est requis");
    }
    
    $id_utilisateur = (int)$donnees_recues['id_utilisateur'];
    
    // Modifier le rôle
    if (isset($donnees_recues['role_utilisateur'])) {
        $resultat = $modele_utilisateur->modifierRoleUtilisateur($id_utilisateur, $donnees_recues['role_utilisateur']);
        
        if ($resultat['succes']) {
            envoyerReponseJSON(200, true, $resultat['message']);
        } else {
            envoyerReponseJSON(400, false, $resultat['message']);
        }
    }
    
    // Modifier le statut
    else if (isset($donnees_recues['est_actif'])) {
        $resultat = $modele_utilisateur->modifierStatutUtilisateur($id_utilisateur, (int)$donnees_recues['est_actif']);
        
        if ($resultat['succes']) {
            envoyerReponseJSON(200, true, $resultat['message']);
        } else {
            envoyerReponseJSON(400, false, $resultat['message']);
        }
    }
    
    else {
        envoyerReponseJSON(400, false, "Aucune modification spécifiée");
    }
}

// ====================
// Méthode non autorisée
// ====================
else {
    envoyerReponseJSON(405, false, "Méthode non autorisée");
}
