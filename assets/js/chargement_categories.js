let categorie_selectionnee = null;
let tous_les_produits = [];

document.addEventListener('DOMContentLoaded', async function() {
    await verifierEtAfficherOutilsAdmin();
    chargerCategories();
    chargerTousLesProduits();
    initialiserRecherche();
});

async function chargerCategories() {
    try {
        const reponse = await fetch('../backend/api/api_categories.php');
        const donnees = await reponse.json();
        
        if (donnees.success && donnees.categories) {
            afficherCategories(donnees.categories);
        }
    } catch (erreur) {
        console.error('Erreur:', erreur);
    }
}

function afficherCategories(categories) {
    const conteneur = document.getElementById('conteneur-categories');
    conteneur.innerHTML = '';
    
    const boutonTous = document.createElement('button');
    boutonTous.className = 'bouton-categorie active';
    boutonTous.textContent = 'Tous les produits';
    boutonTous.onclick = () => {
        categorie_selectionnee = null;
        chargerTousLesProduits();
        document.querySelectorAll('.bouton-categorie').forEach(b => b.classList.remove('active'));
        boutonTous.classList.add('active');
    };
    conteneur.appendChild(boutonTous);
    
    categories.forEach(cat => {
        const bouton = document.createElement('button');
        bouton.className = 'bouton-categorie';
        bouton.textContent = cat.nom_categorie;
        bouton.onclick = () => {
            categorie_selectionnee = cat.id_categorie;
            chargerProduitsParCategorie(cat.id_categorie);
            document.querySelectorAll('.bouton-categorie').forEach(b => b.classList.remove('active'));
            bouton.classList.add('active');
        };
        conteneur.appendChild(bouton);
    });
}

async function chargerTousLesProduits() {
    try {
        const reponse = await fetch('../backend/api/api_produits.php');
        const donnees = await reponse.json();
        
        if (donnees.success && donnees.produits) {
            tous_les_produits = donnees.produits;
            afficherProduits(tous_les_produits);
        }
    } catch (erreur) {
        console.error('Erreur:', erreur);
    }
}

async function chargerProduitsParCategorie(id_categorie) {
    try {
        const reponse = await fetch(`../backend/api/api_produits.php?categorie=${id_categorie}`);
        const donnees = await reponse.json();
        
        if (donnees.success && donnees.produits) {
            afficherProduits(donnees.produits);
        }
    } catch (erreur) {
        console.error('Erreur:', erreur);
    }
}

function afficherProduits(produits) {
    const conteneur = document.getElementById('conteneur-produits');
    conteneur.innerHTML = '';
    
    if (produits.length === 0) {
        conteneur.innerHTML = '<p style="grid-column: 1 / -1; text-align: center;">Aucun produit trouvé</p>';
        return;
    }
    
    produits.forEach(produit => {
        const card = document.createElement('div');
        card.className = 'carte-produit';
        card.onclick = () => ouvrirModalProduit(produit.id_produit);
        
        // Ajuster le chemin de l'image pour la page categories.php
        const cheminImage = produit.image_url_produit 
            ? (produit.image_url_produit.startsWith('assets/') 
                ? '../' + produit.image_url_produit 
                : produit.image_url_produit)
            : null;
        
        // Définir l'image comme background de la carte
        if (cheminImage) {
            card.style.backgroundImage = `url('${cheminImage}')`;
        } else {
            card.style.backgroundColor = '#f5f5f5';
        }
        
        const badge = produit.est_vedette == 1 
            ? '<span class="badge-vedette">⭐ Vedette</span>' 
            : '';
        
        card.innerHTML = `
            ${badge}
            <div class="info-produit">
                <h4>${produit.nom_produit}</h4>
            </div>
        `;
        
        conteneur.appendChild(card);
    });
}

function initialiserRecherche() {
    const champ = document.getElementById('champ-recherche');
    
    if (champ) {
        champ.addEventListener('input', function() {
            const terme = this.value.toLowerCase();
            
            if (terme === '') {
                if (categorie_selectionnee) {
                    chargerProduitsParCategorie(categorie_selectionnee);
                } else {
                    afficherProduits(tous_les_produits);
                }
                return;
            }
            
            const produitsFiltres = tous_les_produits.filter(p => 
                p.nom_produit.toLowerCase().includes(terme) ||
                (p.description_produit && p.description_produit.toLowerCase().includes(terme)) ||
                (p.nom_categorie && p.nom_categorie.toLowerCase().includes(terme))
            );
            
            afficherProduits(produitsFiltres);
        });
    }
}

async function verifierEtAfficherOutilsAdmin() {
    try {
        const reponse = await fetch('../backend/api/api_verifier_session.php');
        const donnees = await reponse.json();
        
        if (donnees.connecte && donnees.utilisateur.role_utilisateur === 'administrateur') {
            afficherBarreOutilsAdmin();
        }
    } catch (erreur) {
        console.log('Non connecté ou erreur:', erreur);
    }
}

function afficherBarreOutilsAdmin() {
    const main = document.querySelector('main#category');
    
    if (!main) return;
    
    const barreOutils = document.createElement('div');
    barreOutils.className = 'barre-outils-admin';
    barreOutils.innerHTML = `
        <h3>Outils Administrateur</h3>
        <button onclick="ouvrirModalAjouterProduit()" class="bouton-admin bouton-admin-ajouter">
            ➕ Ajouter un produit
        </button>
    `;
    
    main.insertBefore(barreOutils, main.firstChild);
    ajouterBoutonsAdminSurProduits();
}

function ajouterBoutonsAdminSurProduits() {
    const observer = new MutationObserver(function() {
        const cartes = document.querySelectorAll('.carte-produit');
        cartes.forEach(carte => {
            if (!carte.querySelector('.boutons-admin-produit')) {
                ajouterBoutonsAdminSurCarte(carte);
            }
        });
    });
    
    observer.observe(document.getElementById('conteneur-produits'), {
        childList: true,
        subtree: true
    });
}

function ajouterBoutonsAdminSurCarte(carte) {
    const boutonsAdmin = document.createElement('div');
    boutonsAdmin.className = 'boutons-admin-produit';
    boutonsAdmin.innerHTML = `
        <button class="bouton-admin-mini bouton-modifier" title="Modifier">✏️</button>
        <button class="bouton-admin-mini bouton-supprimer" title="Supprimer">🗑️</button>
    `;
    
    carte.appendChild(boutonsAdmin);
    boutonsAdmin.addEventListener('click', (e) => e.stopPropagation());
}

function ouvrirModalAjouterProduit() {
    alert('Fonctionnalité d\'ajout de produit à implémenter');
}
