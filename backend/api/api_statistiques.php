<?php
session_start();

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');
header('Access-Control-Allow-Headers: Content-Type');

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../middleware/middleware_authentification.php';

// Vérifier que l'utilisateur est admin
verifierAuthentificationAdmin();

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode([
        'success' => false,
        'message' => 'Méthode non autorisée'
    ]);
    exit;
}

try {
    $db = obtenirConnexionBD();

    // Statistiques globales
    $stats = [];

    // Nombre total de produits
    $stmt = $db->query("SELECT COUNT(*) as total FROM produits");
    $result = $stmt->fetch();
    $stats['total_produits'] = intval($result['total']);

    // Nombre de produits disponibles
    $stmt = $db->query("SELECT COUNT(*) as total FROM produits WHERE quantite_disponible > 0");
    $result = $stmt->fetch();
    $stats['produits_disponibles'] = intval($result['total']);

    // Nombre total d'utilisateurs
    $stmt = $db->query("SELECT COUNT(*) as total FROM utilisateurs WHERE role_utilisateur = 'utilisateur'");
    $result = $stmt->fetch();
    $stats['total_utilisateurs'] = intval($result['total']);

    // Nombre total de réservations
    $stmt = $db->query("SELECT COUNT(*) as total FROM reservations");
    $result = $stmt->fetch();
    $stats['total_reservations'] = intval($result['total']);

    // Réservations en attente
    $stmt = $db->query("SELECT COUNT(*) as total FROM reservations WHERE statut_reservation = 'en_attente'");
    $result = $stmt->fetch();
    $stats['reservations_en_attente'] = intval($result['total']);

    // Réservations acceptées
    $stmt = $db->query("SELECT COUNT(*) as total FROM reservations WHERE statut_reservation = 'accepte'");
    $result = $stmt->fetch();
    $stats['reservations_acceptees'] = intval($result['total']);

    // Nombre de catégories
    $stmt = $db->query("SELECT COUNT(*) as total FROM categories");
    $result = $stmt->fetch();
    $stats['total_categories'] = intval($result['total']);

    // Produits les plus réservés (top 5)
    $stmt = $db->query("
        SELECT
            p.id_produit,
            p.nom_produit,
            p.image_url_produit,
            c.nom_categorie,
            COUNT(r.id_reservation) as nombre_reservations
        FROM produits p
        LEFT JOIN reservations r ON p.id_produit = r.id_produit
        LEFT JOIN categories c ON p.id_categorie = c.id_categorie
        GROUP BY p.id_produit
        ORDER BY nombre_reservations DESC
        LIMIT 5
    ");
    $stats['produits_populaires'] = $stmt->fetchAll();

    // Réservations récentes (10 dernières)
    $stmt = $db->query("
        SELECT
            r.*,
            p.nom_produit,
            p.image_url_produit,
            u.nom_utilisateur,
            u.prenom_utilisateur,
            u.email_utilisateur
        FROM reservations r
        JOIN produits p ON r.id_produit = p.id_produit
        JOIN utilisateurs u ON r.id_utilisateur = u.id_utilisateur
        ORDER BY r.date_reservation DESC
        LIMIT 10
    ");
    $stats['reservations_recentes'] = $stmt->fetchAll();

    // Statistiques mensuelles (12 derniers mois)
    $stmt = $db->query("
        SELECT
            strftime('%Y-%m', date_reservation) as mois,
            COUNT(*) as nombre_reservations
        FROM reservations
        WHERE date_reservation >= date('now', '-12 months')
        GROUP BY strftime('%Y-%m', date_reservation)
        ORDER BY mois ASC
    ");
    $stats['reservations_par_mois'] = $stmt->fetchAll();

    // Répartition des réservations par statut
    $stmt = $db->query("
        SELECT
            statut_reservation,
            COUNT(*) as nombre
        FROM reservations
        GROUP BY statut_reservation
    ");
    $stats['reservations_par_statut'] = $stmt->fetchAll();

    // Catégories avec le nombre de produits
    $stmt = $db->query("
        SELECT
            c.id_categorie,
            c.nom_categorie,
            COUNT(p.id_produit) as nombre_produits
        FROM categories c
        LEFT JOIN produits p ON c.id_categorie = p.id_categorie
        GROUP BY c.id_categorie
        ORDER BY nombre_produits DESC
    ");
    $stats['produits_par_categorie'] = $stmt->fetchAll();

    // Taux de disponibilité global
    $stmt = $db->query("
        SELECT
            SUM(quantite_totale) as total_quantites,
            SUM(quantite_disponible) as total_disponibles
        FROM produits
    ");
    $result = $stmt->fetch();
    $total_quantites = intval($result['total_quantites']);
    $total_disponibles = intval($result['total_disponibles']);

    $stats['taux_disponibilite'] = $total_quantites > 0
        ? round(($total_disponibles / $total_quantites) * 100, 2)
        : 100;

    echo json_encode([
        'success' => true,
        'statistiques' => $stats
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Erreur serveur: ' . $e->getMessage()
    ]);
}
?>
