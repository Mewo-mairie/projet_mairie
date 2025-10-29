<?php
// Modèle pour gérer les utilisateurs

require_once __DIR__ . '/../config/connexion_base_donnees.php';

class ModeleUtilisateur {
    
    // Crée un nouvel utilisateur
    public function creerUtilisateur($email, $mot_de_passe, $prenom, $nom, $role = 'utilisateur') {
        $connexion_bd = obtenirConnexionBD();
        
        // Hasher le mot de passe
        $mot_de_passe_hash = password_hash($mot_de_passe, PASSWORD_DEFAULT);
        
        $requete = "INSERT INTO utilisateurs (email_utilisateur, mot_de_passe_hash, prenom_utilisateur, nom_utilisateur, role_utilisateur)
                    VALUES (:email, :mot_de_passe, :prenom, :nom, :role)";
        
        $stmt = $connexion_bd->prepare($requete);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':mot_de_passe', $mot_de_passe_hash);
        $stmt->bindParam(':prenom', $prenom);
        $stmt->bindParam(':nom', $nom);
        $stmt->bindParam(':role', $role);
        $stmt->execute();
        
        return $connexion_bd->lastInsertId();
    }
    
    // Vérifie si un email existe déjà
    public function emailExiste($email) {
        $connexion_bd = obtenirConnexionBD();
        
        $requete = "SELECT COUNT(*) as nombre FROM utilisateurs WHERE email_utilisateur = :email";
        
        $stmt = $connexion_bd->prepare($requete);
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        
        $resultat = $stmt->fetch();
        return $resultat['nombre'] > 0;
    }
    
    // Récupère un utilisateur par email
    public function obtenirUtilisateurParEmail($email) {
        $connexion_bd = obtenirConnexionBD();
        
        $requete = "SELECT * FROM utilisateurs WHERE email_utilisateur = :email";
        
        $stmt = $connexion_bd->prepare($requete);
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        
        return $stmt->fetch();
    }
    
    // Récupère un utilisateur par ID
    public function obtenirUtilisateurParId($id_utilisateur) {
        $connexion_bd = obtenirConnexionBD();
        
        $requete = "SELECT * FROM utilisateurs WHERE id_utilisateur = :id";
        
        $stmt = $connexion_bd->prepare($requete);
        $stmt->bindParam(':id', $id_utilisateur);
        $stmt->execute();
        
        return $stmt->fetch();
    }
    
    // Récupère tous les utilisateurs
    public function obtenirTousLesUtilisateurs() {
        $connexion_bd = obtenirConnexionBD();
        
        $requete = "SELECT id_utilisateur, email_utilisateur, prenom_utilisateur, nom_utilisateur, role_utilisateur, date_inscription, est_actif 
                    FROM utilisateurs 
                    ORDER BY nom_utilisateur, prenom_utilisateur";
        
        $resultat = $connexion_bd->query($requete);
        return $resultat->fetchAll();
    }
    
    // Vérifie les identifiants de connexion
    public function verifierConnexion($email, $mot_de_passe) {
        $utilisateur = $this->obtenirUtilisateurParEmail($email);
        
        if (!$utilisateur) {
            return false;
        }
        
        if (!$utilisateur['est_actif']) {
            return false;
        }
        
        return password_verify($mot_de_passe, $utilisateur['mot_de_passe_hash']) ? $utilisateur : false;
    }
    
    // Modifie un utilisateur
    public function modifierUtilisateur($id_utilisateur, $email, $prenom, $nom, $role = null) {
        $connexion_bd = obtenirConnexionBD();
        
        if ($role !== null) {
            $requete = "UPDATE utilisateurs 
                        SET email_utilisateur = :email, 
                            prenom_utilisateur = :prenom, 
                            nom_utilisateur = :nom,
                            role_utilisateur = :role
                        WHERE id_utilisateur = :id";
        } else {
            $requete = "UPDATE utilisateurs 
                        SET email_utilisateur = :email, 
                            prenom_utilisateur = :prenom, 
                            nom_utilisateur = :nom
                        WHERE id_utilisateur = :id";
        }
        
        $stmt = $connexion_bd->prepare($requete);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':prenom', $prenom);
        $stmt->bindParam(':nom', $nom);
        if ($role !== null) {
            $stmt->bindParam(':role', $role);
        }
        $stmt->bindParam(':id', $id_utilisateur);
        
        return $stmt->execute();
    }
    
    // Supprime un utilisateur
    public function supprimerUtilisateur($id_utilisateur) {
        $connexion_bd = obtenirConnexionBD();
        
        $requete = "DELETE FROM utilisateurs WHERE id_utilisateur = :id";
        
        $stmt = $connexion_bd->prepare($requete);
        $stmt->bindParam(':id', $id_utilisateur);
        
        return $stmt->execute();
    }
}
