<?php
// Modèle pour gérer les produits

require_once __DIR__ . '/../config/connexion_base_donnees.php';

class ModeleProduit {
    
    // Récupère tous les produits
    public function obtenirTousLesProduits() {
        $connexion_bd = obtenirConnexionBD();
        
        $requete = "SELECT p.*, c.nom_categorie 
                    FROM produits p 
                    LEFT JOIN categories c ON p.id_categorie = c.id_categorie
                    ORDER BY p.nom_produit";
        
        $resultat = $connexion_bd->query($requete);
        return $resultat->fetchAll();
    }
    
    // Récupère un produit par son ID
    public function obtenirProduitParId($id_produit) {
        $connexion_bd = obtenirConnexionBD();
        
        $requete = "SELECT p.*, c.nom_categorie 
                    FROM produits p 
                    LEFT JOIN categories c ON p.id_categorie = c.id_categorie
                    WHERE p.id_produit = :id";
        
        $stmt = $connexion_bd->prepare($requete);
        $stmt->bindParam(':id', $id_produit);
        $stmt->execute();
        
        return $stmt->fetch();
    }
    
    // Récupère les produits par catégorie
    public function obtenirProduitsParCategorie($id_categorie) {
        $connexion_bd = obtenirConnexionBD();
        
        $requete = "SELECT p.*, c.nom_categorie 
                    FROM produits p 
                    LEFT JOIN categories c ON p.id_categorie = c.id_categorie
                    WHERE p.id_categorie = :id_categorie
                    ORDER BY p.nom_produit";
        
        $stmt = $connexion_bd->prepare($requete);
        $stmt->bindParam(':id_categorie', $id_categorie);
        $stmt->execute();
        
        return $stmt->fetchAll();
    }
    
    // Récupère les produits vedettes
    public function obtenirProduitsVedettes() {
        $connexion_bd = obtenirConnexionBD();
        
        $requete = "SELECT p.*, c.nom_categorie 
                    FROM produits p 
                    LEFT JOIN categories c ON p.id_categorie = c.id_categorie
                    WHERE p.est_vedette = 1
                    ORDER BY p.nom_produit";
        
        $resultat = $connexion_bd->query($requete);
        return $resultat->fetchAll();
    }
    
    // Crée un nouveau produit
    public function creerProduit($nom, $description, $id_categorie, $image_url = '', $est_vedette = 0) {
        $connexion_bd = obtenirConnexionBD();
        
        $requete = "INSERT INTO produits (nom_produit, description_produit, id_categorie, image_url_produit, est_vedette)
                    VALUES (:nom, :description, :id_categorie, :image_url, :est_vedette)";
        
        $stmt = $connexion_bd->prepare($requete);
        $stmt->bindParam(':nom', $nom);
        $stmt->bindParam(':description', $description);
        $stmt->bindParam(':id_categorie', $id_categorie);
        $stmt->bindParam(':image_url', $image_url);
        $stmt->bindParam(':est_vedette', $est_vedette);
        $stmt->execute();
        
        return $connexion_bd->lastInsertId();
    }
    
    // Modifie un produit
    public function modifierProduit($id_produit, $nom, $description, $id_categorie, $image_url = '', $est_vedette = 0) {
        $connexion_bd = obtenirConnexionBD();
        
        $requete = "UPDATE produits 
                    SET nom_produit = :nom, 
                        description_produit = :description, 
                        id_categorie = :id_categorie,
                        image_url_produit = :image_url,
                        est_vedette = :est_vedette
                    WHERE id_produit = :id";
        
        $stmt = $connexion_bd->prepare($requete);
        $stmt->bindParam(':nom', $nom);
        $stmt->bindParam(':description', $description);
        $stmt->bindParam(':id_categorie', $id_categorie);
        $stmt->bindParam(':image_url', $image_url);
        $stmt->bindParam(':est_vedette', $est_vedette);
        $stmt->bindParam(':id', $id_produit);
        
        return $stmt->execute();
    }
    
    // Supprime un produit
    public function supprimerProduit($id_produit) {
        $connexion_bd = obtenirConnexionBD();
        
        $requete = "DELETE FROM produits WHERE id_produit = :id";
        
        $stmt = $connexion_bd->prepare($requete);
        $stmt->bindParam(':id', $id_produit);
        
        return $stmt->execute();
    }
}
