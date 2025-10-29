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
</head>
<body>
    <header>
        <div id="logo-div">
            <a href="../index.html">
                <img src="../assets/header_logo.png" alt="Logo Lend&Share" />
            </a>
            <h1>Lend&Share</h1>
        </div>
        <nav class="navigation-header">
            <a href="../index.html">Accueil</a>
            <a href="categories.php" class="lien-actif">Produits</a>
        </nav>
        <button id="connexion" class="bouton-connexion">connexion</button>
    </header>

    <main id="category">
        <h1>Nos produits disponibles</h1>
        
        <div class="conteneur-recherche">
            <input 
                type="search" 
                id="champ-recherche" 
                placeholder="Rechercher un produit..." 
                aria-label="Rechercher un produit"
            >
        </div>
        
        <nav id="conteneur-categories" class="navigation-categories">
            <div class="indicateur-chargement">Chargement des catégories...</div>
        </nav>

        <section id="conteneur-produits" class="grille-produits">
            <div class="indicateur-chargement">Chargement des produits...</div>
        </section>
    </main>

    <footer>
        <section id="footer-container">
            <section>
                <p>mentions legales</p>
            </section>
            <section>
                <img src="../assets/footer_logo.png" alt="Logo bas de page" />
            </section>
            <section>
                <p>contact</p>
            </section>
        </section>
    </footer>

    <script src="../assets/js/chargement_categories.js"></script>
    <script src="../assets/js/modal_produit.js"></script>
    <script src="../assets/js/gestion_session.js"></script>
</body>
</html>
