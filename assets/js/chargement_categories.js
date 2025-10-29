let categorie_selectionnee = null;
let tous_les_produits = [];

document.addEventListener('DOMContentLoaded', function() {
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
        
        const img = produit.image_url_produit 
            ? `<img src="${produit.image_url_produit}" alt="${produit.nom_produit}">`
            : `<div class="placeholder-image">Pas d'image</div>`;
        
        const badge = produit.est_vedette == 1 
            ? '<span class="badge-vedette">⭐ Vedette</span>' 
            : '';
        
        card.innerHTML = `
            ${img}
            <div class="info-produit">
                <h3>${produit.nom_produit}</h3>
                <p class="categorie">${produit.nom_categorie}</p>
                ${badge}
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
