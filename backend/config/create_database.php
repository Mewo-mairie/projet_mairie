<?php
/**
 * Script de création de la base de données SQLite pour Lend&Share
 * Ce script crée toutes les tables nécessaires et insère les données initiales
 * 
 * UTILISATION : Exécuter ce fichier une seule fois pour créer la base de données
 * php create_database.php
 */

// Inclure le fichier de configuration
require_once __DIR__ . '/config.php';

echo "=== Création de la base de données Lend&Share ===\n\n";

try {
    // Créer la connexion à la base de données SQLite
    $connexion_base_donnees = new PDO('sqlite:' . CHEMIN_BASE_DONNEES);
    
    // Activer les erreurs PDO
    $connexion_base_donnees->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Activer les clés étrangères dans SQLite
    $connexion_base_donnees->exec('PRAGMA foreign_keys = ON;');
    
    echo "✓ Connexion à la base de données établie\n\n";
    
    // ====================
    // TABLE UTILISATEURS
    // ====================
    echo "Création de la table 'utilisateurs'...\n";
    $requete_creation_table_utilisateurs = "
        CREATE TABLE IF NOT EXISTS utilisateurs (
            id_utilisateur INTEGER PRIMARY KEY AUTOINCREMENT,
            email_utilisateur TEXT UNIQUE NOT NULL,
            mot_de_passe_hash TEXT NOT NULL,
            prenom_utilisateur TEXT NOT NULL,
            nom_utilisateur TEXT NOT NULL,
            role_utilisateur TEXT DEFAULT 'utilisateur',
            date_inscription DATETIME DEFAULT CURRENT_TIMESTAMP,
            est_actif INTEGER DEFAULT 1
        )
    ";
    $connexion_base_donnees->exec($requete_creation_table_utilisateurs);
    echo "✓ Table 'utilisateurs' créée\n\n";
    
    // ====================
    // TABLE CATEGORIES
    // ====================
    echo "Création de la table 'categories'...\n";
    $requete_creation_table_categories = "
        CREATE TABLE IF NOT EXISTS categories (
            id_categorie INTEGER PRIMARY KEY AUTOINCREMENT,
            nom_categorie TEXT UNIQUE NOT NULL,
            description_categorie TEXT,
            image_url_categorie TEXT,
            date_creation DATETIME DEFAULT CURRENT_TIMESTAMP
        )
    ";
    $connexion_base_donnees->exec($requete_creation_table_categories);
    echo "✓ Table 'categories' créée\n\n";
    
    // ====================
    // TABLE PRODUITS
    // ====================
    echo "Création de la table 'produits'...\n";
    $requete_creation_table_produits = "
        CREATE TABLE IF NOT EXISTS produits (
            id_produit INTEGER PRIMARY KEY AUTOINCREMENT,
            nom_produit TEXT NOT NULL,
            description_produit TEXT,
            id_categorie INTEGER NOT NULL,
            image_url_produit TEXT,
            est_vedette INTEGER DEFAULT 0,
            date_ajout_produit DATETIME DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (id_categorie) REFERENCES categories(id_categorie)
        )
    ";
    $connexion_base_donnees->exec($requete_creation_table_produits);
    echo "✓ Table 'produits' créée\n\n";
    
    // ====================
    // TABLE RESERVATIONS
    // ====================
    echo "Création de la table 'reservations'...\n";
    $requete_creation_table_reservations = "
        CREATE TABLE IF NOT EXISTS reservations (
            id_reservation INTEGER PRIMARY KEY AUTOINCREMENT,
            id_utilisateur INTEGER NOT NULL,
            id_produit INTEGER NOT NULL,
            date_reservation DATETIME DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (id_utilisateur) REFERENCES utilisateurs(id_utilisateur),
            FOREIGN KEY (id_produit) REFERENCES produits(id_produit)
        )
    ";
    $connexion_base_donnees->exec($requete_creation_table_reservations);
    echo "✓ Table 'reservations' créée\n\n";
    
    // ====================
    // DONNÉES INITIALES
    // ====================
    echo "=== Insertion des données initiales ===\n\n";
    
    // Créer un compte administrateur par défaut
    // Mot de passe : Admin123! (à changer immédiatement après la première connexion)
    echo "Création du compte administrateur...\n";
    $mot_de_passe_admin = 'Admin123!';
    $mot_de_passe_admin_hash = password_hash($mot_de_passe_admin, PASSWORD_DEFAULT);
    
    $requete_creation_admin = "
        INSERT INTO utilisateurs (
            email_utilisateur,
            mot_de_passe_hash,
            prenom_utilisateur,
            nom_utilisateur,
            role_utilisateur
        ) VALUES (
            'admin@lendshare.fr',
            :mot_de_passe_hash,
            'Administrateur',
            'Système',
            'administrateur'
        )
    ";
    
    $statement_admin = $connexion_base_donnees->prepare($requete_creation_admin);
    $statement_admin->bindParam(':mot_de_passe_hash', $mot_de_passe_admin_hash);
    $statement_admin->execute();
    
    echo "✓ Compte administrateur créé\n";
    echo "  Email : admin@lendshare.fr\n";
    echo "  Mot de passe : Admin123!\n";
    echo "  ⚠️  IMPORTANT : Changez ce mot de passe après la première connexion !\n\n";
    
    // Créer les catégories par défaut
    echo "Création des catégories par défaut...\n";
    $categories_par_defaut = [
        [
            'nom' => 'Barnums',
            'description' => 'Tentes et barnums pour événements extérieurs'
        ],
        [
            'nom' => 'Chaises',
            'description' => 'Chaises pliantes et mobilier d\'assise'
        ],
        [
            'nom' => 'Outils',
            'description' => 'Outils de bricolage et jardinage'
        ],
        [
            'nom' => 'Vidéoprojecteurs',
            'description' => 'Matériel de projection et audiovisuel'
        ]
    ];
    
    $requete_insertion_categorie = "
        INSERT INTO categories (nom_categorie, description_categorie)
        VALUES (:nom, :description)
    ";
    
    $statement_categorie = $connexion_base_donnees->prepare($requete_insertion_categorie);
    
    foreach ($categories_par_defaut as $categorie) {
        $statement_categorie->bindParam(':nom', $categorie['nom']);
        $statement_categorie->bindParam(':description', $categorie['description']);
        $statement_categorie->execute();
        echo "  ✓ Catégorie '{$categorie['nom']}' créée\n";
    }
    
    echo "\n✓ Toutes les catégories ont été créées\n\n";
    
    // Créer quelques produits d'exemple
    echo "Création de produits d'exemple...\n";
    $produits_exemple = [
        [
            'nom' => 'Barnum 3x3m blanc',
            'description' => 'Barnum pliant de 3x3 mètres, couleur blanche',
            'categorie' => 'Barnums',
            'est_vedette' => 1
        ],
        [
            'nom' => 'Barnum 4x4m bleu',
            'description' => 'Grand barnum de 4x4 mètres, couleur bleue',
            'categorie' => 'Barnums',
            'est_vedette' => 0
        ],
        [
            'nom' => 'Chaise pliante blanche',
            'description' => 'Chaise pliante en plastique blanc',
            'categorie' => 'Chaises',
            'est_vedette' => 1
        ],
        [
            'nom' => 'Chaise pliante noire',
            'description' => 'Chaise pliante en plastique noir',
            'categorie' => 'Chaises',
            'est_vedette' => 0
        ],
        [
            'nom' => 'Perceuse visseuse',
            'description' => 'Perceuse visseuse sans fil 18V avec batterie',
            'categorie' => 'Outils',
            'est_vedette' => 1
        ],
        [
            'nom' => 'Tondeuse électrique',
            'description' => 'Tondeuse à gazon électrique 1600W',
            'categorie' => 'Outils',
            'est_vedette' => 0
        ],
        [
            'nom' => 'Vidéoprojecteur HD',
            'description' => 'Vidéoprojecteur Full HD avec câbles HDMI',
            'categorie' => 'Vidéoprojecteurs',
            'est_vedette' => 1
        ],
        [
            'nom' => 'Écran de projection',
            'description' => 'Écran de projection portable 200x150cm',
            'categorie' => 'Vidéoprojecteurs',
            'est_vedette' => 0
        ]
    ];
    
    foreach ($produits_exemple as $produit) {
        // Récupérer l'ID de la catégorie
        $requete_id_categorie = "SELECT id_categorie FROM categories WHERE nom_categorie = :nom_categorie";
        $statement_id_categorie = $connexion_base_donnees->prepare($requete_id_categorie);
        $statement_id_categorie->bindParam(':nom_categorie', $produit['categorie']);
        $statement_id_categorie->execute();
        $id_categorie = $statement_id_categorie->fetchColumn();
        
        // Insérer le produit
        $requete_insertion_produit = "
            INSERT INTO produits (
                nom_produit,
                description_produit,
                id_categorie,
                est_vedette
            ) VALUES (
                :nom,
                :description,
                :id_categorie,
                :est_vedette
            )
        ";
        
        $statement_produit = $connexion_base_donnees->prepare($requete_insertion_produit);
        $statement_produit->bindParam(':nom', $produit['nom']);
        $statement_produit->bindParam(':description', $produit['description']);
        $statement_produit->bindParam(':id_categorie', $id_categorie);
        $statement_produit->bindParam(':est_vedette', $produit['est_vedette']);
        $statement_produit->execute();
        
        echo "  ✓ Produit '{$produit['nom']}' créé\n";
    }
    
    echo "\n✓ Produits d'exemple créés\n\n";
    
    echo "===========================================\n";
    echo "✓✓✓ BASE DE DONNÉES CRÉÉE AVEC SUCCÈS ✓✓✓\n";
    echo "===========================================\n\n";
    echo "Fichier de base de données : " . CHEMIN_BASE_DONNEES . "\n\n";
    echo "Vous pouvez maintenant utiliser l'application Lend&Share.\n";
    
} catch (PDOException $erreur) {
    echo "❌ ERREUR lors de la création de la base de données :\n";
    echo $erreur->getMessage() . "\n";
    exit(1);
}
