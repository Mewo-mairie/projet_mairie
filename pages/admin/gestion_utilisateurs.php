<?php
/**
 * Page de gestion des utilisateurs (admin)
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
    <title>Gestion Utilisateurs - Admin Lend&Share</title>
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
            <a href="gestion_categories.php" class="menu-item">
                <span class="icone">🏷️</span>
                <span>Catégories</span>
            </a>
            <a href="gestion_reservations.php" class="menu-item">
                <span class="icone">📅</span>
                <span>Réservations</span>
            </a>
            <a href="gestion_utilisateurs.php" class="menu-item actif">
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
            <h1>👥 Gestion des utilisateurs</h1>
        </header>

        <!-- Filtres -->
        <section class="section-filtres">
            <div class="barre-recherche-admin">
                <input 
                    type="search" 
                    id="recherche-utilisateurs" 
                    placeholder="Rechercher un utilisateur (nom, email...)"
                    class="champ-recherche-admin"
                >
            </div>
            <div class="filtres-categories">
                <label>Filtrer par rôle :</label>
                <select id="filtre-role" class="select-admin">
                    <option value="">Tous les rôles</option>
                    <option value="utilisateur">Utilisateurs</option>
                    <option value="administrateur">Administrateurs</option>
                </select>
            </div>
            <div class="filtres-disponibilite">
                <label>Statut :</label>
                <select id="filtre-statut" class="select-admin">
                    <option value="">Tous</option>
                    <option value="actif">Actifs</option>
                    <option value="inactif">Inactifs</option>
                </select>
            </div>
        </section>

        <!-- Tableau des utilisateurs -->
        <section class="section-dashboard">
            <div id="conteneur-tableau-utilisateurs">
                <p class="message-chargement">Chargement des utilisateurs...</p>
            </div>
        </section>
    </main>

    <!-- Modal détails utilisateur -->
    <div id="modal-details-utilisateur" class="modal-overlay" style="display: none;">
        <div class="modal-contenu modal-admin-medium">
            <button class="modal-bouton-fermer" onclick="fermerModalDetails()">&times;</button>
            
            <h2>Détails de l'utilisateur</h2>
            
            <div id="contenu-details-utilisateur">
                <!-- Chargé dynamiquement -->
            </div>
            
            <div class="actions-formulaire">
                <button class="bouton-action bouton-secondaire" onclick="fermerModalDetails()">
                    Fermer
                </button>
            </div>
        </div>
    </div>

    <script src="../../assets/js/admin/gestion_utilisateurs.js"></script>
</body>
</html>
