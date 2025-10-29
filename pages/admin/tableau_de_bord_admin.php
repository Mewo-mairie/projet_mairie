<?php
/**
 * Page du tableau de bord administrateur
 * Affiche les statistiques et permet la navigation vers les sections de gestion
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
    <title>Dashboard Admin - Lend&Share</title>
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
            <a href="tableau_de_bord_admin.php" class="menu-item actif">
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
            <h1>Tableau de bord</h1>
            <div class="actions-header">
                <span class="date-actuelle" id="date-actuelle"></span>
            </div>
        </header>

        <!-- Cartes de statistiques -->
        <section class="grille-stats">
            <!-- Chargement des statistiques via JavaScript -->
            <div class="carte-stat skeleton">
                <div class="skeleton-line"></div>
                <div class="skeleton-number"></div>
            </div>
            <div class="carte-stat skeleton">
                <div class="skeleton-line"></div>
                <div class="skeleton-number"></div>
            </div>
            <div class="carte-stat skeleton">
                <div class="skeleton-line"></div>
                <div class="skeleton-number"></div>
            </div>
            <div class="carte-stat skeleton">
                <div class="skeleton-line"></div>
                <div class="skeleton-number"></div>
            </div>
        </section>

        <!-- Section réservations en attente -->
        <section class="section-dashboard">
            <div class="entete-section">
                <h2>⏳ Réservations en attente</h2>
                <a href="gestion_reservations.php" class="lien-voir-tout">Voir tout →</a>
            </div>
            <div id="conteneur-reservations-attente" class="conteneur-tableau">
                <p class="message-chargement">Chargement...</p>
            </div>
        </section>

        <!-- Section produits populaires -->
        <section class="section-dashboard">
            <div class="entete-section">
                <h2>🔥 Produits les plus réservés</h2>
                <a href="gestion_produits.php" class="lien-voir-tout">Gérer →</a>
            </div>
            <div id="conteneur-produits-populaires" class="grille-produits-populaires">
                <p class="message-chargement">Chargement...</p>
            </div>
        </section>

        <!-- Section statistiques mensuelles -->
        <section class="section-dashboard">
            <div class="entete-section">
                <h2>📈 Évolution des réservations</h2>
            </div>
            <div id="conteneur-graphique-mensuel" class="conteneur-graphique">
                <p class="message-chargement">Chargement...</p>
            </div>
        </section>
    </main>

    <script src="../../assets/js/admin/dashboard.js"></script>
</body>
</html>
