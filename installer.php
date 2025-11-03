<?php
/**
 * SCRIPT D'INSTALLATION AUTOMATIQUE - LendShare Mairie
 * 
 * Ce script installe automatiquement l'application LendShare.
 * Il vérifie les prérequis, crée la base de données, configure les dossiers
 * et initialise les comptes administrateur.
 * 
 * UTILISATION :
 * 1. Placer ce fichier à la racine du projet
 * 2. Exécuter : php installer.php
 * 3. Suivre les instructions à l'écran
 * 
 * @author LendShare Team
 * @version 1.0.0
 */

// Configuration de l'affichage pour debug
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Couleurs pour la console (Windows compatible)
define('COULEUR_VERT', "\033[32m");
define('COULEUR_ROUGE', "\033[31m");
define('COULEUR_JAUNE', "\033[33m");
define('COULEUR_BLEU', "\033[34m");
define('COULEUR_RESET', "\033[0m");

/**
 * Affiche un message avec une couleur et un symbole
 */
function afficher_message($message, $type = 'info') {
    $symboles = [
        'succes' => '[OK]',
        'erreur' => '[ERREUR]',
        'info'   => '[INFO]',
        'warning' => '[ATTENTION]',
        'etape'  => '[ETAPE]'
    ];
    
    $couleurs = [
        'succes' => COULEUR_VERT,
        'erreur' => COULEUR_ROUGE,
        'info'   => COULEUR_BLEU,
        'warning' => COULEUR_JAUNE,
        'etape'  => COULEUR_BLEU
    ];
    
    $symbole = $symboles[$type] ?? '';
    $couleur = $couleurs[$type] ?? COULEUR_RESET;
    
    echo $couleur . $symbole . ' ' . $message . COULEUR_RESET . "\n";
}

/**
 * Affiche un titre de section
 */
function afficher_titre($titre) {
    echo "\n" . str_repeat('=', 70) . "\n";
    echo "  " . $titre . "\n";
    echo str_repeat('=', 70) . "\n\n";
}

/**
 * Vérifie si une extension PHP est chargée
 */
function verifier_extension_php($extension_nom) {
    if (extension_loaded($extension_nom)) {
        afficher_message("Extension PHP '$extension_nom' : Installée", 'succes');
        return true;
    } else {
        afficher_message("Extension PHP '$extension_nom' : MANQUANTE", 'erreur');
        return false;
    }
}

/**
 * Crée un dossier s'il n'existe pas
 */
function creer_dossier_si_absent($chemin_dossier, $description) {
    if (!file_exists($chemin_dossier)) {
        if (mkdir($chemin_dossier, 0755, true)) {
            afficher_message("Dossier '$description' créé : $chemin_dossier", 'succes');
            return true;
        } else {
            afficher_message("Impossible de créer le dossier '$description' : $chemin_dossier", 'erreur');
            return false;
        }
    } else {
        afficher_message("Dossier '$description' existe déjà", 'info');
        return true;
    }
}

/**
 * ÉTAPE 1 : Vérification des prérequis système
 */
function etape_1_verifier_prerequis() {
    afficher_titre("ÉTAPE 1/6 : Vérification des prérequis système");
    
    $prerequis_ok = true;
    
    // Vérifier version PHP
    $version_php = phpversion();
    afficher_message("Version PHP : $version_php", 'info');
    
    if (version_compare($version_php, '7.4.0', '>=')) {
        afficher_message("Version PHP compatible (>= 7.4)", 'succes');
    } else {
        afficher_message("Version PHP trop ancienne. Minimum requis : 7.4", 'erreur');
        $prerequis_ok = false;
    }
    
    // Vérifier extensions PHP requises
    $extensions_requises = ['pdo', 'pdo_sqlite', 'json', 'mbstring'];
    
    foreach ($extensions_requises as $extension) {
        if (!verifier_extension_php($extension)) {
            $prerequis_ok = false;
        }
    }
    
    if (!$prerequis_ok) {
        afficher_message("\n⛔ Prérequis non satisfaits. Installation impossible.", 'erreur');
        afficher_message("Installez les extensions manquantes et relancez l'installation.", 'warning');
        exit(1);
    }
    
    afficher_message("\n✅ Tous les prérequis sont satisfaits !", 'succes');
    return true;
}

/**
 * ÉTAPE 2 : Création de la structure des dossiers
 */
function etape_2_creer_structure_dossiers() {
    afficher_titre("ÉTAPE 2/6 : Création de la structure des dossiers");
    
    $dossiers_a_creer = [
        'database' => 'Base de données',
        'cache' => 'Cache',
        'logs' => 'Logs système',
        'uploads' => 'Uploads',
        'uploads/products' => 'Images produits',
        'uploads/categories' => 'Images catégories',
        'backups' => 'Sauvegardes',
        'backend/tests' => 'Scripts de test'
    ];
    
    $tous_crees = true;
    
    foreach ($dossiers_a_creer as $chemin => $description) {
        if (!creer_dossier_si_absent($chemin, $description)) {
            $tous_crees = false;
        }
    }
    
    // Créer fichier .gitkeep dans les dossiers vides
    $dossiers_gitkeep = ['database', 'cache', 'logs', 'backups'];
    foreach ($dossiers_gitkeep as $dossier) {
        $fichier_gitkeep = $dossier . '/.gitkeep';
        if (!file_exists($fichier_gitkeep)) {
            file_put_contents($fichier_gitkeep, '');
            afficher_message("Fichier .gitkeep créé dans $dossier", 'info');
        }
    }
    
    return $tous_crees;
}

/**
 * ÉTAPE 3 : Création du fichier .gitignore
 */
function etape_3_creer_gitignore() {
    afficher_titre("ÉTAPE 3/6 : Création du fichier .gitignore");
    
    $contenu_gitignore = <<<EOT
# Base de données
database/*.db
database/*.db-journal

# Cache
cache/*
!cache/.gitkeep

# Logs
logs/*
!logs/.gitkeep

# Uploads
uploads/products/*
uploads/categories/*
!uploads/products/.gitkeep
!uploads/categories/.gitkeep

# Backups
backups/*
!backups/.gitkeep

# Fichiers temporaires
*.tmp
*.temp
*.swp
*.bak

# Dossiers de sauvegarde
projet_mairie_backup/

# IDE
.vscode/
.idea/
*.code-workspace

# OS
.DS_Store
Thumbs.db
desktop.ini

# Scripts de test et debug (option)
backend/config/test_*.php
backend/config/check_*.php
backend/config/fix_*.php

EOT;
    
    if (file_put_contents('.gitignore', $contenu_gitignore)) {
        afficher_message("Fichier .gitignore créé avec succès", 'succes');
        return true;
    } else {
        afficher_message("Erreur lors de la création de .gitignore", 'erreur');
        return false;
    }
}

/**
 * ÉTAPE 4 : Initialisation de la base de données
 */
function etape_4_initialiser_base_donnees() {
    afficher_titre("ÉTAPE 4/6 : Initialisation de la base de données");
    
    $fichier_creation_bd = 'backend/config/create_database.php';
    
    if (file_exists($fichier_creation_bd)) {
        afficher_message("Exécution du script de création de la base de données...", 'etape');
        
        // Exécuter le script de création
        require_once $fichier_creation_bd;
        
        // Vérifier que la base a bien été créée
        if (file_exists('database/lendshare.db')) {
            afficher_message("Base de données créée avec succès", 'succes');
            
            // Afficher la taille du fichier
            $taille_bd = filesize('database/lendshare.db');
            $taille_ko = round($taille_bd / 1024, 2);
            afficher_message("Taille de la base de données : {$taille_ko} Ko", 'info');
            
            return true;
        } else {
            afficher_message("La base de données n'a pas été créée", 'erreur');
            return false;
        }
    } else {
        afficher_message("Script de création de base de données introuvable", 'erreur');
        afficher_message("Fichier recherché : $fichier_creation_bd", 'info');
        return false;
    }
}

/**
 * ÉTAPE 5 : Configuration des comptes utilisateur
 */
function etape_5_configurer_comptes() {
    afficher_titre("ÉTAPE 5/6 : Configuration des comptes utilisateur");
    
    afficher_message("Les comptes par défaut sont déjà créés dans la base de données", 'info');
    afficher_message("", 'info');
    afficher_message("Comptes disponibles :", 'info');
    afficher_message("", 'info');
    afficher_message("  Administrateur :", 'info');
    afficher_message("     Email    : admin@lendshare.fr", 'info');
    afficher_message("     Password : Admin123!", 'warning');
    afficher_message("", 'info');
    afficher_message("  Utilisateur test :", 'info');
    afficher_message("     Email    : test@test.fr", 'info');
    afficher_message("     Password : test123", 'warning');
    afficher_message("", 'info');
    afficher_message("IMPORTANT : Changez ces mots de passe en production !", 'warning');
    
    return true;
}

/**
 * ÉTAPE 6 : Vérification finale et tests
 */
function etape_6_verification_finale() {
    afficher_titre("ÉTAPE 6/6 : Vérification finale");
    
    $verifications = [
        'database/lendshare.db' => 'Base de données',
        'backend/config/database.php' => 'Configuration base de données',
        'backend/api/api_connexion.php' => 'API de connexion',
        'assets/js/connexion.js' => 'Script de connexion',
        'pages/connexion.html' => 'Page de connexion',
        'index.html' => 'Page d\'accueil'
    ];
    
    $tous_ok = true;
    
    foreach ($verifications as $fichier => $description) {
        if (file_exists($fichier)) {
            afficher_message("$description : OK", 'succes');
        } else {
            afficher_message("$description : MANQUANT", 'erreur');
            $tous_ok = false;
        }
    }
    
    return $tous_ok;
}

/**
 * Affiche le résumé final
 */
function afficher_resume_final() {
    afficher_titre("INSTALLATION TERMINÉE AVEC SUCCÈS !");
    
    echo "\n";
    afficher_message("L'application LendShare est maintenant installée et prête à l'emploi.", 'succes');
    echo "\n";
    
    echo "PROCHAINES ÉTAPES :\n\n";
    echo "  1. Démarrer un serveur web PHP :\n";
    echo "     " . COULEUR_VERT . "php -S localhost:8000" . COULEUR_RESET . "\n\n";
    echo "  2. Ouvrir votre navigateur :\n";
    echo "     " . COULEUR_BLEU . "http://localhost:8000" . COULEUR_RESET . "\n\n";
    echo "  3. Se connecter avec les comptes de test :\n";
    echo "     Admin : admin@lendshare.fr / Admin123!\n";
    echo "     User  : test@test.fr / test123\n\n";
    
    echo "DOCUMENTATION :\n";
    echo "  - Cahier des charges : Cahier_des_charges_LendShare_Mairie 1.pdf\n";
    echo "  - État des lieux    : ETAT_DES_LIEUX.md\n";
    echo "  - Comptes de test   : accounts.MD\n\n";
    
    echo "RAPPEL SÉCURITÉ :\n";
    echo "  - Changez les mots de passe par défaut en production\n";
    echo "  - Configurez un serveur web (Apache/Nginx) pour la production\n";
    echo "  - Activez HTTPS pour sécuriser les connexions\n\n";
    
    afficher_message("Bon développement !", 'succes');
    echo "\n";
}

// ============================================================================
// PROGRAMME PRINCIPAL
// ============================================================================

echo "\n";
echo "========================================================================\n";
echo "                                                                        \n";
echo "        INSTALLATEUR AUTOMATIQUE - LENDSHARE MAIRIE                   \n";
echo "                                                                        \n";
echo "                     Version 1.0.0 - 2025                              \n";
echo "                                                                        \n";
echo "========================================================================\n";
echo "\n";

afficher_message("Début de l'installation automatique...", 'info');
afficher_message("Cela peut prendre quelques secondes.", 'info');

// Exécution des étapes
$etapes = [
    'etape_1_verifier_prerequis',
    'etape_2_creer_structure_dossiers',
    'etape_3_creer_gitignore',
    'etape_4_initialiser_base_donnees',
    'etape_5_configurer_comptes',
    'etape_6_verification_finale'
];

$installation_reussie = true;

foreach ($etapes as $etape_fonction) {
    if (!$etape_fonction()) {
        $installation_reussie = false;
        afficher_message("\n❌ L'installation a échoué à l'étape : $etape_fonction", 'erreur');
        exit(1);
    }
}

// Afficher le résumé final si tout s'est bien passé
if ($installation_reussie) {
    afficher_resume_final();
}
?>
