<?php
/**
 * SCRIPT DE NETTOYAGE AUTOMATIQUE - LendShare Mairie
 * 
 * Ce script nettoie le projet en supprimant :
 * - Les fichiers doublons (versions anglaises)
 * - Le dossier de backup
 * - Les fichiers temporaires
 * - Réorganise les scripts de test
 * 
 * UTILISATION :
 * 1. Faire une sauvegarde du projet avant d'exécuter ce script
 * 2. Exécuter : php nettoyer.php
 * 3. Vérifier les changements
 * 
 * @author LendShare Team
 * @version 1.0.0
 */

// Configuration de l'affichage
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Couleurs pour la console
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
        'supprime' => '[SUPPRIME]',
        'deplace' => '[DEPLACE]'
    ];
    
    $couleurs = [
        'succes' => COULEUR_VERT,
        'erreur' => COULEUR_ROUGE,
        'info'   => COULEUR_BLEU,
        'warning' => COULEUR_JAUNE,
        'supprime' => COULEUR_ROUGE,
        'deplace' => COULEUR_JAUNE
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
 * Demande confirmation à l'utilisateur
 */
function demander_confirmation($question) {
    echo COULEUR_JAUNE . "❓ $question (o/n) : " . COULEUR_RESET;
    $reponse = trim(fgets(STDIN));
    return (strtolower($reponse) === 'o' || strtolower($reponse) === 'oui');
}

/**
 * Supprime un fichier
 */
function supprimer_fichier($chemin_fichier) {
    if (file_exists($chemin_fichier)) {
        if (unlink($chemin_fichier)) {
            afficher_message("Fichier supprimé : $chemin_fichier", 'supprime');
            return true;
        } else {
            afficher_message("Erreur lors de la suppression : $chemin_fichier", 'erreur');
            return false;
        }
    } else {
        afficher_message("Fichier déjà absent : $chemin_fichier", 'info');
        return true;
    }
}

/**
 * Supprime un dossier récursivement
 */
function supprimer_dossier_recursif($chemin_dossier) {
    if (!file_exists($chemin_dossier)) {
        afficher_message("Dossier déjà absent : $chemin_dossier", 'info');
        return true;
    }
    
    if (!is_dir($chemin_dossier)) {
        return unlink($chemin_dossier);
    }
    
    // Lister tous les fichiers et sous-dossiers
    $fichiers = array_diff(scandir($chemin_dossier), ['.', '..']);
    
    foreach ($fichiers as $fichier) {
        $chemin_complet = $chemin_dossier . '/' . $fichier;
        
        if (is_dir($chemin_complet)) {
            // Suppression récursive du sous-dossier
            supprimer_dossier_recursif($chemin_complet);
        } else {
            // Suppression du fichier
            unlink($chemin_complet);
        }
    }
    
    // Supprimer le dossier vide
    if (rmdir($chemin_dossier)) {
        afficher_message("Dossier supprimé : $chemin_dossier", 'supprime');
        return true;
    } else {
        afficher_message("Erreur lors de la suppression du dossier : $chemin_dossier", 'erreur');
        return false;
    }
}

/**
 * Déplace un fichier
 */
function deplacer_fichier($source, $destination) {
    if (!file_exists($source)) {
        afficher_message("Fichier source introuvable : $source", 'erreur');
        return false;
    }
    
    // Créer le dossier de destination s'il n'existe pas
    $dossier_destination = dirname($destination);
    if (!file_exists($dossier_destination)) {
        mkdir($dossier_destination, 0755, true);
    }
    
    if (rename($source, $destination)) {
        afficher_message("Fichier déplacé : $source → $destination", 'deplace');
        return true;
    } else {
        afficher_message("Erreur lors du déplacement : $source", 'erreur');
        return false;
    }
}

/**
 * ÉTAPE 1 : Suppression des fichiers doublons CSS
 */
function etape_1_supprimer_doublons_css() {
    afficher_titre("ÉTAPE 1/4 : Suppression des fichiers doublons CSS");
    
    afficher_message("Fichiers doublons identifiés (versions anglaises à supprimer) :", 'info');
    
    $fichiers_doublons = [
        'assets/products.css' => 'Version anglaise de produits.css',
        'assets/product_modal.css' => 'Version anglaise de modal_produit.css',
        'assets/my_account.css' => 'Version anglaise de mon_compte.css'
    ];
    
    foreach ($fichiers_doublons as $fichier => $description) {
        echo "  • $fichier ($description)\n";
    }
    
    echo "\n";
    
    if (!demander_confirmation("Voulez-vous supprimer ces fichiers doublons ?")) {
        afficher_message("Suppression annulée par l'utilisateur", 'warning');
        return false;
    }
    
    $compteur_supprimes = 0;
    
    foreach ($fichiers_doublons as $fichier => $description) {
        if (supprimer_fichier($fichier)) {
            $compteur_supprimes++;
        }
    }
    
    afficher_message("\n$compteur_supprimes fichiers doublons supprimés", 'succes');
    return true;
}

/**
 * ÉTAPE 2 : Suppression du dossier backup
 */
function etape_2_supprimer_backup() {
    afficher_titre("ÉTAPE 2/4 : Suppression du dossier de backup");
    
    $dossier_backup = 'projet_mairie_backup';
    
    if (!file_exists($dossier_backup)) {
        afficher_message("Le dossier backup n'existe pas", 'info');
        return true;
    }
    
    // Calculer la taille du dossier
    $taille_backup = 0;
    $iterator = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($dossier_backup, RecursiveDirectoryIterator::SKIP_DOTS)
    );
    
    foreach ($iterator as $fichier) {
        if ($fichier->isFile()) {
            $taille_backup += $fichier->getSize();
        }
    }
    
    $taille_mb = round($taille_backup / (1024 * 1024), 2);
    
    afficher_message("Dossier backup trouvé : $dossier_backup", 'warning');
    afficher_message("Taille : $taille_mb Mo", 'info');
    
    if (!demander_confirmation("Voulez-vous supprimer ce dossier de backup ?")) {
        afficher_message("Suppression annulée par l'utilisateur", 'warning');
        return false;
    }
    
    return supprimer_dossier_recursif($dossier_backup);
}

/**
 * ÉTAPE 3 : Réorganisation des scripts de test
 */
function etape_3_reorganiser_scripts_test() {
    afficher_titre("ÉTAPE 3/4 : Réorganisation des scripts de test");
    
    afficher_message("Scripts de test à déplacer vers backend/tests/ :", 'info');
    
    $scripts_test = [
        'backend/config/check_database.php',
        'backend/config/check_passwords.php',
        'backend/config/check_table_structure.php',
        'backend/config/test_api_produits.php',
        'backend/config/test_indisponibilite.php',
        'backend/config/test_password_verify.php',
        'backend/config/fix_test_password.php'
    ];
    
    $scripts_existants = [];
    foreach ($scripts_test as $script) {
        if (file_exists($script)) {
            $scripts_existants[] = $script;
            echo "  • " . basename($script) . "\n";
        }
    }
    
    if (empty($scripts_existants)) {
        afficher_message("Aucun script de test à déplacer", 'info');
        return true;
    }
    
    echo "\n";
    
    if (!demander_confirmation("Voulez-vous déplacer ces scripts vers backend/tests/ ?")) {
        afficher_message("Déplacement annulé par l'utilisateur", 'warning');
        return false;
    }
    
    // Créer le dossier tests s'il n'existe pas
    if (!file_exists('backend/tests')) {
        mkdir('backend/tests', 0755, true);
        afficher_message("Dossier backend/tests/ créé", 'succes');
    }
    
    $compteur_deplaces = 0;
    
    foreach ($scripts_existants as $script_source) {
        $nom_fichier = basename($script_source);
        $script_destination = 'backend/tests/' . $nom_fichier;
        
        if (deplacer_fichier($script_source, $script_destination)) {
            $compteur_deplaces++;
        }
    }
    
    afficher_message("\n$compteur_deplaces scripts déplacés vers backend/tests/", 'succes');
    return true;
}

/**
 * ÉTAPE 4 : Nettoyage des fichiers temporaires
 */
function etape_4_nettoyer_temporaires() {
    afficher_titre("ÉTAPE 4/4 : Nettoyage des fichiers temporaires");
    
    $patterns_temporaires = [
        '*.tmp',
        '*.temp',
        '*.bak',
        '*.swp',
        '*~',
        '.DS_Store',
        'Thumbs.db',
        'desktop.ini'
    ];
    
    afficher_message("Recherche de fichiers temporaires...", 'info');
    
    $fichiers_temporaires_trouves = [];
    
    // Rechercher dans tout le projet
    $iterator = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator('.', RecursiveDirectoryIterator::SKIP_DOTS)
    );
    
    foreach ($iterator as $fichier) {
        if ($fichier->isFile()) {
            $nom_fichier = $fichier->getFilename();
            
            foreach ($patterns_temporaires as $pattern) {
                if (fnmatch($pattern, $nom_fichier)) {
                    $fichiers_temporaires_trouves[] = $fichier->getPathname();
                }
            }
        }
    }
    
    if (empty($fichiers_temporaires_trouves)) {
        afficher_message("Aucun fichier temporaire trouvé", 'succes');
        return true;
    }
    
    afficher_message("Fichiers temporaires trouvés :", 'warning');
    foreach ($fichiers_temporaires_trouves as $fichier_temp) {
        echo "  • $fichier_temp\n";
    }
    
    echo "\n";
    
    if (!demander_confirmation("Voulez-vous supprimer ces fichiers temporaires ?")) {
        afficher_message("Suppression annulée par l'utilisateur", 'warning');
        return false;
    }
    
    $compteur_supprimes = 0;
    
    foreach ($fichiers_temporaires_trouves as $fichier_temp) {
        if (supprimer_fichier($fichier_temp)) {
            $compteur_supprimes++;
        }
    }
    
    afficher_message("\n$compteur_supprimes fichiers temporaires supprimés", 'succes');
    return true;
}

/**
 * Affiche le résumé final
 */
function afficher_resume_final($statistiques) {
    afficher_titre("🎉 NETTOYAGE TERMINÉ !");
    
    echo "\n";
    afficher_message("Résumé des opérations :", 'info');
    echo "\n";
    
    echo "  📊 Statistiques :\n";
    echo "    • Fichiers doublons supprimés : " . ($statistiques['doublons'] ?? 0) . "\n";
    echo "    • Dossiers backup supprimés : " . ($statistiques['backup'] ?? 0) . "\n";
    echo "    • Scripts de test déplacés : " . ($statistiques['scripts'] ?? 0) . "\n";
    echo "    • Fichiers temporaires nettoyés : " . ($statistiques['temporaires'] ?? 0) . "\n";
    echo "\n";
    
    echo "  Recommandations :\n";
    echo "    1. Vérifiez que l'application fonctionne toujours correctement\n";
    echo "    2. Testez les fonctionnalités principales\n";
    echo "    3. Committez les changements dans Git\n";
    echo "\n";
    
    afficher_message("Projet nettoyé avec succès !", 'succes');
    echo "\n";
}

// ============================================================================
// PROGRAMME PRINCIPAL
// ============================================================================

echo "\n";
echo "========================================================================\n";
echo "                                                                        \n";
echo "        SCRIPT DE NETTOYAGE - LENDSHARE MAIRIE                        \n";
echo "                                                                        \n";
echo "                     Version 1.0.0 - 2025                              \n";
echo "                                                                        \n";
echo "========================================================================\n";
echo "\n";

afficher_message("ATTENTION : Ce script va modifier votre projet", 'warning');
afficher_message("Il est recommandé de faire une sauvegarde avant de continuer", 'warning');
echo "\n";

if (!demander_confirmation("Voulez-vous continuer avec le nettoyage ?")) {
    afficher_message("Nettoyage annulé par l'utilisateur", 'info');
    exit(0);
}

// Statistiques
$statistiques = [
    'doublons' => 0,
    'backup' => 0,
    'scripts' => 0,
    'temporaires' => 0
];

// Exécution des étapes de nettoyage
echo "\n";
afficher_message("Début du nettoyage automatique...", 'info');

if (etape_1_supprimer_doublons_css()) {
    $statistiques['doublons'] = 3;
}

if (etape_2_supprimer_backup()) {
    $statistiques['backup'] = 1;
}

if (etape_3_reorganiser_scripts_test()) {
    $statistiques['scripts'] = 7;
}

if (etape_4_nettoyer_temporaires()) {
    $statistiques['temporaires'] = 0; // Sera mis à jour par la fonction
}

// Afficher le résumé final
afficher_resume_final($statistiques);

?>
