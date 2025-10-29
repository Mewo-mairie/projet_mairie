<?php
// API pour gérer les réservations

session_start();

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, DELETE');
header('Access-Control-Allow-Headers: Content-Type');

require_once __DIR__ . '/../models/modele_reservation.php';

$modele_reservation = new ModeleReservation();
$methode = $_SERVER['REQUEST_METHOD'];

try {
    switch ($methode) {
        case 'GET':
            if (isset($_GET['id'])) {
                // Récupérer une réservation par ID
                $reservation = $modele_reservation->obtenirReservationParId($_GET['id']);
                echo json_encode(['success' => true, 'reservation' => $reservation]);
            } elseif (isset($_GET['utilisateur'])) {
                // Récupérer les réservations d'un utilisateur
                $reservations = $modele_reservation->obtenirReservationsUtilisateur($_GET['utilisateur']);
                echo json_encode(['success' => true, 'reservations' => $reservations]);
            } else {
                // Récupérer toutes les réservations (pour admin)
                $reservations = $modele_reservation->obtenirToutesLesReservations();
                echo json_encode(['success' => true, 'reservations' => $reservations]);
            }
            break;
            
        case 'POST':
            // Créer une nouvelle réservation
            $donnees = json_decode(file_get_contents('php://input'), true);
            
            // Vérifier si une réservation existe déjà
            $existe = $modele_reservation->verifierReservationExistante(
                $donnees['id_utilisateur'],
                $donnees['id_produit']
            );
            
            if ($existe) {
                echo json_encode([
                    'success' => false, 
                    'message' => 'Vous avez déjà réservé ce produit'
                ]);
                break;
            }
            
            $id = $modele_reservation->creerReservation(
                $donnees['id_utilisateur'],
                $donnees['id_produit']
            );
            
            echo json_encode(['success' => true, 'message' => 'Réservation créée', 'id' => $id]);
            break;
            
        case 'DELETE':
            // Supprimer une réservation (annuler)
            $donnees = json_decode(file_get_contents('php://input'), true);
            
            $resultat = $modele_reservation->supprimerReservation($donnees['id_reservation']);
            
            echo json_encode(['success' => $resultat, 'message' => 'Réservation annulée']);
            break;
            
        default:
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Méthode non autorisée']);
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Erreur serveur: ' . $e->getMessage()]);
}
