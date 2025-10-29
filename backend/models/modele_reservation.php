<?php
// Modèle pour gérer les réservations

require_once __DIR__ . '/../config/connexion_base_donnees.php';

class ModeleReservation {
    
    // Crée une nouvelle réservation
    public function creerReservation($id_utilisateur, $id_produit) {
        $connexion_bd = obtenirConnexionBD();
        
        $requete = "INSERT INTO reservations (id_utilisateur, id_produit)
                    VALUES (:id_utilisateur, :id_produit)";
        
        $stmt = $connexion_bd->prepare($requete);
        $stmt->bindParam(':id_utilisateur', $id_utilisateur);
        $stmt->bindParam(':id_produit', $id_produit);
        $stmt->execute();
        
        return $connexion_bd->lastInsertId();
    }
    
    // Récupère toutes les réservations
    public function obtenirToutesLesReservations() {
        $connexion_bd = obtenirConnexionBD();
        
        $requete = "SELECT r.*, 
                           u.nom_utilisateur, u.prenom_utilisateur, u.email_utilisateur,
                           p.nom_produit, p.image_url_produit,
                           c.nom_categorie
                    FROM reservations r
                    LEFT JOIN utilisateurs u ON r.id_utilisateur = u.id_utilisateur
                    LEFT JOIN produits p ON r.id_produit = p.id_produit
                    LEFT JOIN categories c ON p.id_categorie = c.id_categorie
                    ORDER BY r.date_reservation DESC";
        
        $resultat = $connexion_bd->query($requete);
        return $resultat->fetchAll();
    }
    
    // Récupère les réservations d'un utilisateur
    public function obtenirReservationsUtilisateur($id_utilisateur) {
        $connexion_bd = obtenirConnexionBD();
        
        $requete = "SELECT r.*, 
                           p.nom_produit, p.image_url_produit,
                           c.nom_categorie
                    FROM reservations r
                    LEFT JOIN produits p ON r.id_produit = p.id_produit
                    LEFT JOIN categories c ON p.id_categorie = c.id_categorie
                    WHERE r.id_utilisateur = :id_utilisateur
                    ORDER BY r.date_reservation DESC";
        
        $stmt = $connexion_bd->prepare($requete);
        $stmt->bindParam(':id_utilisateur', $id_utilisateur);
        $stmt->execute();
        
        return $stmt->fetchAll();
    }
    
    // Récupère une réservation par son ID
    public function obtenirReservationParId($id_reservation) {
        $connexion_bd = obtenirConnexionBD();
        
        $requete = "SELECT r.*, 
                           u.nom_utilisateur, u.prenom_utilisateur, u.email_utilisateur,
                           p.nom_produit, p.image_url_produit,
                           c.nom_categorie
                    FROM reservations r
                    LEFT JOIN utilisateurs u ON r.id_utilisateur = u.id_utilisateur
                    LEFT JOIN produits p ON r.id_produit = p.id_produit
                    LEFT JOIN categories c ON p.id_categorie = c.id_categorie
                    WHERE r.id_reservation = :id";
        
        $stmt = $connexion_bd->prepare($requete);
        $stmt->bindParam(':id', $id_reservation);
        $stmt->execute();
        
        return $stmt->fetch();
    }
    
    // Supprime une réservation
    public function supprimerReservation($id_reservation) {
        $connexion_bd = obtenirConnexionBD();
        
        $requete = "DELETE FROM reservations WHERE id_reservation = :id";
        
        $stmt = $connexion_bd->prepare($requete);
        $stmt->bindParam(':id', $id_reservation);
        
        return $stmt->execute();
    }
    
    // Vérifie si un produit est déjà réservé par un utilisateur
    public function verifierReservationExistante($id_utilisateur, $id_produit) {
        $connexion_bd = obtenirConnexionBD();
        
        $requete = "SELECT COUNT(*) as nombre 
                    FROM reservations 
                    WHERE id_utilisateur = :id_utilisateur 
                    AND id_produit = :id_produit";
        
        $stmt = $connexion_bd->prepare($requete);
        $stmt->bindParam(':id_utilisateur', $id_utilisateur);
        $stmt->bindParam(':id_produit', $id_produit);
        $stmt->execute();
        
        $resultat = $stmt->fetch();
        return $resultat['nombre'] > 0;
    }
}
