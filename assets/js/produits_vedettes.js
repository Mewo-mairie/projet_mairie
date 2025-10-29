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
        card.className = 'tile-categorie';
        card.style.cursor = 'pointer';
        
        const img = produit.image_url_produit 
            ? `<img src="${produit.image_url_produit}" alt="${produit.nom_produit}">`
            : `<div style="background: #eee; height: 200px; display: flex; align-items: center; justify-content: center;">
                   <span style="color: #999;">Pas d'image</span>
               </div>`;
        
        card.innerHTML = `
            ${img}
            <h4>${produit.nom_produit}</h4>
            <p style="font-size: 0.9em; color: #666;">${produit.nom_categorie}</p>
        `;
        
        card.addEventListener('click', () => {
            window.location.href = `pages/categories.php?categorie=${produit.id_categorie}#produit-${produit.id_produit}`;
        });
        
        grille.appendChild(card);
    });
}
