<?php
// API pour vérifier si une session est active

session_start();

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode(['connecte' => false, 'message' => 'Méthode non autorisée']);
    exit;
}

// Vérifier si l'utilisateur est connecté
if (isset($_SESSION['utilisateur_id']) && !empty($_SESSION['utilisateur_id'])) {
    echo json_encode([
        'connecte' => true,
        'utilisateur' => [
            'id_utilisateur' => $_SESSION['utilisateur_id'],
            'email_utilisateur' => $_SESSION['utilisateur_email'] ?? '',
            'prenom_utilisateur' => $_SESSION['utilisateur_prenom'] ?? '',
            'nom_utilisateur' => $_SESSION['utilisateur_nom'] ?? '',
            'role_utilisateur' => $_SESSION['utilisateur_role'] ?? 'utilisateur'
        ]
    ]);
} else {
    echo json_encode(['connecte' => false]);
}
