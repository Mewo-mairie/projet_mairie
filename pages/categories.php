<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Catégories - Lend&Share</title>
    <link rel="stylesheet" href="../assets/common.css">
    <link rel="stylesheet" href="../assets/category.css">
    <link rel="stylesheet" href="../assets/produits.css">
    <link rel="stylesheet" href="../assets/modal_produit.css">
    <link href='https://fonts.googleapis.com/css?family=Roboto' rel='stylesheet'>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body>
    <header>
        <!-- Le contenu sera généré par header.js -->
    </header>

    <main id="category" role="main">
        <h1>Nos produits disponibles</h1>

        <div class="conteneur-recherche" role="search">
            <input
                type="search"
                id="champ-recherche"
                placeholder="Rechercher un produit..."
                aria-label="Rechercher un produit"
            >
        </div>

        <nav id="conteneur-categories" class="navigation-categories" aria-label="Navigation des catégories de produits">
            <div class="indicateur-chargement" role="status" aria-live="polite">Chargement des catégories...</div>
        </nav>

        <section id="conteneur-produits" class="grille-produits" aria-label="Liste des produits disponibles">
            <div class="indicateur-chargement" role="status" aria-live="polite">Chargement des produits...</div>
        </section>
    </main>

    <footer>
        <section id="footer-container">
            <section class="footer-section">
                <h3><a href="mentions-legales.html" style="color: white; text-decoration: none;">Mentions Légales</a></h3>
            </section>
            <section class="footer-section footer-logo">
                <img src="../assets/footer_logo.png" alt="Logo Lend&Share" />
            </section>
            <section class="footer-section">
                <h3><a href="contact.html" style="color: white; text-decoration: none;">Contact</a></h3>
            </section>
        </section>
    </footer>

    <script src="../assets/js/header.js"></script>
    <script src="../assets/js/chargement_categories.js"></script>
    <script src="../assets/js/modal_produit.js"></script>
    <script src="../assets/js/gestion_session.js"></script>
</body>
</html>
