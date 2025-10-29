<?php
/**
 * Page de visualisation des logs d'actions administrateur
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
    <title>Logs Admin - Lend&Share</title>
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
            <a href="gestion_utilisateurs.php" class="menu-item">
                <span class="icone">👥</span>
                <span>Utilisateurs</span>
            </a>
            <a href="logs_admin.php" class="menu-item actif">
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
            <h1>📝 Logs d'actions administrateur</h1>
        </header>

        <!-- Filtres -->
        <section class="section-filtres">
            <div class="barre-recherche-admin">
                <input 
                    type="search" 
                    id="recherche-logs" 
                    placeholder="Rechercher dans les logs..."
                    class="champ-recherche-admin"
                >
            </div>
            <div class="info-pagination">
                <span id="info-total">-</span> entrées
            </div>
        </section>

        <!-- Tableau des logs -->
        <section class="section-dashboard">
            <div id="conteneur-tableau-logs">
                <p class="message-chargement">Chargement des logs...</p>
            </div>
        </section>

        <!-- Pagination -->
        <div id="pagination-logs" class="pagination-container">
            <!-- Chargé dynamiquement -->
        </div>
    </main>

    <script src="../../assets/js/admin/logs_admin.js"></script>
</body>
</html>
