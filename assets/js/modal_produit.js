let produit_actuel = null;

async function ouvrirModalProduit(id_produit) {
    try {
        const reponse = await fetch(`../backend/api/api_produits.php?id=${id_produit}`);
        const donnees = await reponse.json();
        
        if (donnees.success && donnees.produit) {
            produit_actuel = donnees.produit;
            afficherModalProduit(produit_actuel);
        } else {
            alert('Impossible de charger les détails du produit');
        }
    } catch (erreur) {
        console.error('Erreur:', erreur);
        alert('Erreur de connexion au serveur');
    }
}

function afficherModalProduit(produit) {
    let modal = document.getElementById('modal-produit');
    
    if (!modal) {
        modal = creerStructureModal();
        document.body.appendChild(modal);
    }
    
    const image = modal.querySelector('#modal-image-produit');
    const nom = modal.querySelector('#modal-nom-produit');
    const categorie = modal.querySelector('#modal-categorie-produit');
    const description = modal.querySelector('#modal-description-produit');
    const badge = modal.querySelector('#modal-badge-disponibilite');
    const conteneur = modal.querySelector('#conteneur-formulaire-reservation');
    
    if (produit.image_url_produit) {
        const cheminImage = produit.image_url_produit.startsWith('assets/') 
            ? '../' + produit.image_url_produit 
            : produit.image_url_produit;
        image.src = cheminImage;
        image.alt = produit.nom_produit;
    } else {
        image.src = '../assets/images/default-product.png';
        image.alt = 'Pas d\'image';
    }
    
    nom.textContent = produit.nom_produit;
    categorie.textContent = produit.nom_categorie || 'Sans catégorie';
    description.textContent = produit.description_produit || 'Aucune description disponible';
    
    if (produit.est_vedette == 1) {
        badge.textContent = '⭐ Produit vedette';
        badge.className = 'badge-modal badge-modal-vedette';
    } else {
        badge.textContent = '';
        badge.className = 'badge-modal';
    }
    
    afficherFormulaireReservation(conteneur);
    
    modal.style.display = 'flex';
    document.body.style.overflow = 'hidden';
}

function creerStructureModal() {
    const modal = document.createElement('div');
    modal.id = 'modal-produit';
    modal.className = 'modal-overlay';
    
    modal.innerHTML = `
        <div class="modal-contenu">
            <button class="modal-bouton-fermer" onclick="fermerModalProduit()">&times;</button>
            
            <div class="modal-grille">
                <div class="modal-section-image">
                    <img id="modal-image-produit" src="" alt="">
                    <span id="modal-badge-disponibilite" class="badge-modal"></span>
                </div>
                
                <div class="modal-section-infos">
                    <h2 id="modal-nom-produit"></h2>
                    <p class="modal-categorie" id="modal-categorie-produit"></p>
                    <p class="modal-description" id="modal-description-produit"></p>
                    
                    <div id="conteneur-formulaire-reservation"></div>
                </div>
            </div>
        </div>
    `;
    
    modal.addEventListener('click', function(event) {
        if (event.target === modal) {
            fermerModalProduit();
        }
    });
    
    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape' && modal.style.display === 'flex') {
            fermerModalProduit();
        }
    });
    
    return modal;
}

function afficherFormulaireReservation(conteneur) {
    conteneur.innerHTML = `
        <div class="formulaire-reservation">
            <h4>Réserver ce produit</h4>
            
            <div id="message-reservation-modal" class="message-cache"></div>
            
            <button 
                id="bouton-reserver-modal" 
                class="bouton-reserver-modal"
                onclick="soumettreReservation()"
            >
                Réserver
            </button>
        </div>
    `;
}

async function soumettreReservation() {
    const session = await verifierSession();
    
    if (!session) {
        afficherMessage('Vous devez être connecté pour réserver', 'erreur');
        setTimeout(() => {
            window.location.href = 'connexion.html';
        }, 2000);
        return;
    }
    
    const bouton = document.getElementById('bouton-reserver-modal');
    bouton.disabled = true;
    bouton.textContent = 'Réservation en cours...';
    
    try {
        const reponse = await fetch('../backend/api/api_reservations.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                id_utilisateur: session.id_utilisateur,
                id_produit: produit_actuel.id_produit
            })
        });
        
        const donnees = await reponse.json();
        
        if (donnees.success) {
            afficherMessage('Réservation effectuée avec succès !', 'succes');
            
            setTimeout(() => {
                fermerModalProduit();
                if (typeof chargerTousLesProduits === 'function') {
                    chargerTousLesProduits();
                }
            }, 2000);
        } else {
            afficherMessage(donnees.message, 'erreur');
            bouton.disabled = false;
            bouton.textContent = 'Réserver';
        }
    } catch (erreur) {
        console.error('Erreur:', erreur);
        afficherMessage('Erreur lors de la réservation', 'erreur');
        bouton.disabled = false;
        bouton.textContent = 'Réserver';
    }
}

async function verifierSession() {
    try {
        const reponse = await fetch('../backend/api/api_verifier_session.php');
        const donnees = await reponse.json();
        return donnees.connecte ? donnees.utilisateur : null;
    } catch (erreur) {
        console.error('Erreur:', erreur);
        return null;
    }
}

function afficherMessage(message, type) {
    const zone = document.getElementById('message-reservation-modal');
    
    if (!zone) return;
    
    zone.className = 'zone-message-modal';
    
    if (type === 'succes') {
        zone.classList.add('message-succes');
    } else if (type === 'erreur') {
        zone.classList.add('message-erreur');
    }
    
    zone.textContent = message;
    zone.style.display = 'block';
}

function fermerModalProduit() {
    const modal = document.getElementById('modal-produit');
    
    if (modal) {
        modal.style.display = 'none';
        document.body.style.overflow = '';
    }
    
    produit_actuel = null;
}
