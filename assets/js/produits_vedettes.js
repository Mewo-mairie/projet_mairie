document.addEventListener('DOMContentLoaded', function() {
    chargerProduitsVedettes();
});

function chargerProduitsVedettes() {
    fetch('backend/api/api_produits.php?vedettes=1')
        .then(response => response.json())
        .then(data => {
            if (data.success && data.produits && data.produits.length > 0) {
                afficherProduitsVedettes(data.produits);
            } else {
                document.getElementById('grille-vedettes').innerHTML = 
                    '<p style="grid-column: 1 / -1; text-align: center; color: #999;">Aucun produit vedette pour le moment</p>';
            }
        })
        .catch(error => {
            console.error('Erreur lors du chargement des produits vedettes:', error);
            document.getElementById('grille-vedettes').innerHTML = 
                '<p style="grid-column: 1 / -1; text-align: center; color: #999;">Erreur de chargement</p>';
        });
}

function afficherProduitsVedettes(produits) {
    const grille = document.getElementById('grille-vedettes');
    grille.innerHTML = '';
    
    produits.forEach(produit => {
        const card = document.createElement('div');
        card.className = 'carte-produit';
        card.style.cursor = 'pointer';
        
        // Définir l'image comme background de la carte
        if (produit.image_url_produit) {
            card.style.backgroundImage = `url('${produit.image_url_produit}')`;
        } else {
            card.style.backgroundColor = '#f5f5f5';
        }
        
        // Badge vedette toujours affiché pour les produits vedettes
        const badge = '<span class="badge-vedette">⭐ Vedette</span>';
        
        card.innerHTML = `
            ${badge}
            <div class="info-produit">
                <h3>${produit.nom_produit}</h3>
            </div>
        `;
        
        card.addEventListener('click', () => {
            window.location.href = `pages/categories.php?categorie=${produit.id_categorie}#produit-${produit.id_produit}`;
        });
        
        grille.appendChild(card);
    });
}
