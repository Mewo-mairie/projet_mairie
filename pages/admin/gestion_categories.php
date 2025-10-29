<?php
/**
 * Page de gestion des catégories (admin)
 * Permet de créer, modifier, supprimer des catégories
 */

// Démarrer la session
session_start();

// Vérifier que l'utilisateur est connecté et est admin
if (!isset($_SESSION['utilisateur_connecte']) || $_SESSION['role_utilisateur'] !== 'administrateur') {
    header('Location: ../connexion.html');
    exit;
}

$utilisateur = $_SESSION;
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion Catégories - Admin Lend&Share</title>
    <link rel="stylesheet" href="../../assets/common.css">
    <link rel="stylesheet" href="../../assets/admin.css">
    <link href='https://fonts.googleapis.com/css?family=Roboto' rel='stylesheet'>
</head>
<body class="page-admin">
    <!-- Sidebar navigation -->
    <aside class="sidebar-admin">
        <div class="sidebar-header">
            <h2>🎛️ Admin</h2>
            <p class="nom-admin"><?php echo htmlspecialchars($utilisateur['prenom_utilisateur'] . ' ' . $utilisateur['nom_utilisateur']); ?></p>
        </div>
        
        <nav class="menu-admin">
            <a href="tableau_de_bord_admin.php" class="menu-item">
                <span class="icone">📊</span>
                <span>Dashboard</span>
            </a>
            <a href="gestion_produits.php" class="menu-item">
                <span class="icone">📦</span>
                <span>Produits</span>
            </a>
            <a href="gestion_categories.php" class="menu-item actif">
                <span class="icone">🏷️</span>
                <span>Catégories</span>
            </a>
            <a href="gestion_reservations.php" class="menu-item">
                <span class="icone">📅</span>
                <span>Réservations</span>
            </a>
            <a href="gestion_utilisateurs.php" class="menu-item">
                <span class="icone">👥</span>
                <span>Utilisateurs</span>
            </a>
            <a href="logs_admin.php" class="menu-item">
                <span class="icone">📝</span>
                <span>Logs</span>
            </a>
        </nav>
        
        <div class="sidebar-footer">
            <a href="../categories.php" class="bouton-retour-site">
                ← Retour au site
            </a>
        </div>
    </aside>

    <!-- Contenu principal -->
    <main class="contenu-principal-admin">
        <header class="header-admin">
            <h1>🏷️ Gestion des catégories</h1>
            <div class="actions-header">
                <button id="bouton-ajouter-categorie" class="bouton-action bouton-primaire">
                    + Ajouter une catégorie
                </button>
            </div>
        </header>

        <!-- Grille des catégories -->
        <section class="section-dashboard">
            <div id="conteneur-grille-categories">
                <p class="message-chargement">Chargement des catégories...</p>
            </div>
        </section>
    </main>

    <!-- Modal ajout/modification catégorie -->
    <div id="modal-categorie-admin" class="modal-overlay" style="display: none;">
        <div class="modal-contenu modal-admin-medium">
            <button class="modal-bouton-fermer" onclick="fermerModalCategorieAdmin()">&times;</button>
            
            <h2 id="modal-titre">Ajouter une catégorie</h2>
            
            <form id="formulaire-categorie-admin" class="formulaire-admin">
                <input type="hidden" id="id-categorie-modif" value="">
                
                <div class="groupe-champ">
                    <label for="nom-categorie">Nom de la catégorie *</label>
                    <input 
                        type="text" 
                        id="nom-categorie" 
                        required
                        placeholder="Ex: Barnums"
                    >
                </div>
                
                <div class="groupe-champ">
                    <label for="description-categorie">Description</label>
                    <textarea 
                        id="description-categorie" 
                        rows="4"
                        placeholder="Description de la catégorie..."
                    ></textarea>
                </div>
                
                <div class="groupe-champ">
                    <label>Image de la catégorie</label>
                    <div class="zone-upload-image">
                        <input 
                            type="file" 
                            id="input-image-categorie" 
                            accept="image/*"
                            style="display: none;"
                        >
                        <div id="apercu-image-categorie" class="apercu-image">
                            <p>Cliquez pour sélectionner une image</p>
                            <small>JPEG, PNG, GIF, WEBP - Max 5 Mo</small>
                        </div>
                        <input type="hidden" id="url-image-categorie" value="">
                    </div>
                </div>
                
                <div id="message-formulaire-categorie" class="message-cache"></div>
                
                <div class="actions-formulaire">
                    <button type="button" class="bouton-action bouton-secondaire" onclick="fermerModalCategorieAdmin()">
                        Annuler
                    </button>
                    <button type="submit" class="bouton-action bouton-primaire" id="bouton-submit-categorie">
                        Enregistrer
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script src="../../assets/js/admin/gestion_categories.js"></script>
</body>
</html>
