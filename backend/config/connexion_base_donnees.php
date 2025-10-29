<?php
/**
 * Classe pour gérer la connexion à la base de données SQLite
 * Cette classe utilise le pattern Singleton pour garantir une seule connexion
 */

// Inclure le fichier de configuration
require_once __DIR__ . '/config.php';

class ConnexionBaseDeDonnees {
    
    /**
     * Instance unique de la connexion (pattern Singleton)
     */
    private static $instance_connexion = null;
    
    /**
     * Objet PDO pour la connexion à la base de données
     */
    private $connexion_pdo;
    
    /**
     * Constructeur privé pour empêcher l'instanciation directe
     * Crée la connexion à la base de données SQLite
     */
    private function __construct() {
        try {
            // Vérifier si le fichier de base de données existe
            if (!file_exists(CHEMIN_BASE_DONNEES)) {
                throw new Exception("Le fichier de base de données n'existe pas. Veuillez exécuter create_database.php d'abord.");
            }
            
            // Créer la connexion PDO vers SQLite
            $this->connexion_pdo = new PDO('sqlite:' . CHEMIN_BASE_DONNEES);
            
            // Configurer PDO pour lancer des exceptions en cas d'erreur
            $this->connexion_pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            
            // Activer les clés étrangères dans SQLite
            $this->connexion_pdo->exec('PRAGMA foreign_keys = ON;');
            
            // Configurer le mode de récupération par défaut (tableau associatif)
            $this->connexion_pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            
        } catch (PDOException $erreur_pdo) {
            // En cas d'erreur de connexion PDO
            error_log("Erreur de connexion à la base de données : " . $erreur_pdo->getMessage());
            throw new Exception("Impossible de se connecter à la base de données.");
        } catch (Exception $erreur) {
            // En cas d'autres erreurs
            error_log("Erreur : " . $erreur->getMessage());
            throw $erreur;
        }
    }
    
    /**
     * Méthode pour obtenir l'instance unique de la connexion (Singleton)
     * 
     * @return ConnexionBaseDeDonnees Instance unique de la classe
     */
    public static function obtenirInstance() {
        if (self::$instance_connexion === null) {
            self::$instance_connexion = new self();
        }
        return self::$instance_connexion;
    }
    
    /**
     * Méthode pour obtenir l'objet PDO de connexion
     * 
     * @return PDO Objet PDO pour exécuter des requêtes
     */
    public function obtenirConnexion() {
        return $this->connexion_pdo;
    }
    
    /**
     * Méthode pour tester si la connexion fonctionne
     * 
     * @return bool true si la connexion fonctionne, false sinon
     */
    public function verifierConnexionFonctionne() {
        try {
            // Essayer d'exécuter une requête simple
            $resultat = $this->connexion_pdo->query("SELECT 1");
            return $resultat !== false;
        } catch (PDOException $erreur) {
            error_log("Erreur lors du test de connexion : " . $erreur->getMessage());
            return false;
        }
    }
    
    /**
     * Méthode pour obtenir la version de SQLite
     * 
     * @return string Version de SQLite
     */
    public function obtenirVersionSQLite() {
        try {
            $resultat = $this->connexion_pdo->query("SELECT sqlite_version() as version");
            $donnees = $resultat->fetch();
            return $donnees['version'];
        } catch (PDOException $erreur) {
            return "Version inconnue";
        }
    }
    
    /**
     * Empêcher le clonage de l'instance (Singleton)
     */
    private function __clone() {
        // Ne rien faire
    }
    
    /**
     * Empêcher la désérialisation de l'instance (Singleton)
     */
    public function __wakeup() {
        throw new Exception("Impossible de désérialiser un Singleton");
    }
}

/**
 * Fonction utilitaire pour obtenir rapidement la connexion PDO
 * 
 * @return PDO Objet PDO pour exécuter des requêtes
 */
function obtenirConnexionBD() {
    $instance = ConnexionBaseDeDonnees::obtenirInstance();
    return $instance->obtenirConnexion();
}
