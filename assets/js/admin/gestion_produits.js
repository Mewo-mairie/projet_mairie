/**
 * Script JavaScript pour la gestion des produits (admin)
 */

// Variables globales
let tous_les_produits = [];
let toutes_les_categories = [];
let produit_en_cours_edition = null;

document.addEventListener('DOMContentLoaded', function() {
    initialiserPageProduits();
});

/**
 * Initialise la page de gestion des produits
 */
async function initialiserPageProduits() {
    // Charger les catégories
    await chargerCategories();
    
    // Charger les produits
    await chargerTousLesProduits();
    
    // Initialiser les événements
    initialiserEvenements();
}

/**
 * Charge toutes les catégories
 */
async function chargerCategories() {
    try {
        const reponse = await fetch('../../backend/api/api_categories.php');
        const donnees = await reponse.json();
        
        if (donnees.succes && donnees.donnees) {
            toutes_les_categories = donnees.donnees;
            remplirSelectCategories();
        }
    } catch (erreur) {
        console.error('Erreur lors du chargement des catégories:', erreur);
    }
}

/**
 * Remplit les selects de catégories
 */
function remplirSelectCategories() {
    const select_filtre = document.getElementById('filtre-categorie');
    const select_formulaire = document.getElementById('categorie-produit');
    
    if (!select_filtre || !select_formulaire) return;
    
    // Filtre
    toutes_les_categories.forEach(cat => {
        const option = document.createElement('option');
        option.value = cat.id_categorie;
        option.textContent = cat.nom_categorie;
        select_filtre.appendChild(option);
    });
    
    // Formulaire
    toutes_les_categories.forEach(cat => {
        const option = document.createElement('option');
        option.value = cat.id_categorie;
        option.textContent = cat.nom_categorie;
        select_formulaire.appendChild(option);
    });
}

/**
 * Charge tous les produits
 */
async function chargerTousLesProduits() {
    try {
        const reponse = await fetch('../../backend/api/api_produits.php');
        const donnees = await reponse.json();
        
        if (donnees.succes && donnees.donnees) {
            tous_les_produits = donnees.donnees;
            afficherTableauProduits(tous_les_produits);
        }
    } catch (erreur) {
        console.error('Erreur lors du chargement des produits:', erreur);
        afficherMessage('Erreur lors du chargement', 'erreur');
    }
}

/**
 * Affiche le tableau des produits
 */
function afficherTableauProduits(produits) {
    const conteneur = document.getElementById('conteneur-tableau-produits');
    if (!conteneur) return;
    
    if (produits.length === 0) {
        conteneur.innerHTML = '<p class="message-vide">Aucun produit trouvé</p>';
        return;
    }
    
    let html = `
        <table class="tableau-admin">
            <thead>
                <tr>
                    <th>Image</th>
                    <th>Nom</th>
                    <th>Catégorie</th>
                    <th>Stock</th>
                    <th>Disponible</th>
                    <th>Statut</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
    `;
    
    produits.forEach(produit => {
        const image_url = produit.image_url_produit || '../../assets/images/default-product.png';
        const badge_classe = produit.est_disponible == 1 && produit.quantite_disponible > 0 
            ? 'badge-disponible-admin' 
            : 'badge-indisponible-admin';
        const badge_texte = produit.est_disponible == 1 && produit.quantite_disponible > 0 
            ? 'Disponible' 
            : 'Indisponible';
        
        html += `
            <tr>
                <td>
                    <img src="${image_url}" alt="${escapeHtml(produit.nom_produit)}" class="mini-image-produit" onerror="this.src='../../assets/images/default-product.png'">
                </td>
                <td><strong>${escapeHtml(produit.nom_produit)}</strong></td>
                <td>${escapeHtml(produit.nom_categorie || 'Sans catégorie')}</td>
                <td>${produit.quantite_disponible} / ${produit.quantite_totale}</td>
                <td>
                    <label class="switch-disponibilite">
                        <input 
                            type="checkbox" 
                            ${produit.est_disponible == 1 ? 'checked' : ''}
                            onchange="toggleDisponibilite(${produit.id_produit}, this.checked)"
                        >
                        <span class="slider-disponibilite"></span>
                    </label>
                </td>
                <td><span class="${badge_classe}">${badge_texte}</span></td>
                <td class="actions-tableau">
                    <button 
                        class="bouton-action-petit bouton-modifier" 
                        onclick="ouvrirModalModification(${produit.id_produit})"
                        title="Modifier"
                    >
                        ✏️
                    </button>
                    <button 
                        class="bouton-action-petit bouton-supprimer" 
                        onclick="confirmerSuppression(${produit.id_produit}, '${escapeHtml(produit.nom_produit)}')"
                        title="Supprimer"
                    >
                        🗑️
                    </button>
                </td>
            </tr>
        `;
    });
    
    html += `
            </tbody>
        </table>
    `;
    
    conteneur.innerHTML = html;
}

/**
 * Initialise les événements
 */
function initialiserEvenements() {
    // Bouton ajouter produit
    const bouton_ajouter = document.getElementById('bouton-ajouter-produit');
    if (bouton_ajouter) {
        bouton_ajouter.addEventListener('click', ouvrirModalAjout);
    }
    
    // Formulaire produit
    const formulaire = document.getElementById('formulaire-produit-admin');
    if (formulaire) {
        formulaire.addEventListener('submit', soumettreFormulaireProduit);
    }
    
    // Upload d'image
    const zone_apercu = document.getElementById('apercu-image');
    const input_image = document.getElementById('input-image-produit');
    
    if (zone_apercu && input_image) {
        zone_apercu.addEventListener('click', () => input_image.click());
        input_image.addEventListener('change', gererUploadImage);
    }
    
    // Recherche
    const champ_recherche = document.getElementById('recherche-produits');
    if (champ_recherche) {
        champ_recherche.addEventListener('input', filtrerProduits);
    }
    
    // Filtres
    const filtre_categorie = document.getElementById('filtre-categorie');
    const filtre_disponibilite = document.getElementById('filtre-disponibilite');
    
    if (filtre_categorie) {
        filtre_categorie.addEventListener('change', filtrerProduits);
    }
    
    if (filtre_disponibilite) {
        filtre_disponibilite.addEventListener('change', filtrerProduits);
    }
}

/**
 * Ouvre le modal pour ajouter un produit
 */
function ouvrirModalAjout() {
    produit_en_cours_edition = null;
    
    document.getElementById('modal-titre').textContent = 'Ajouter un produit';
    document.getElementById('formulaire-produit-admin').reset();
    document.getElementById('id-produit-modif').value = '';
    document.getElementById('url-image-produit').value = '';
    document.getElementById('apercu-image').innerHTML = `
        <p>Cliquez pour sélectionner une image</p>
        <small>JPEG, PNG, GIF, WEBP - Max 5 Mo</small>
    `;
    
    document.getElementById('modal-produit-admin').style.display = 'flex';
}

/**
 * Ouvre le modal pour modifier un produit
 */
async function ouvrirModalModification(id_produit) {
    try {
        const reponse = await fetch(`../../backend/api/api_produits.php?id=${id_produit}`);
        const donnees = await reponse.json();
        
        if (donnees.succes && donnees.donnees) {
            produit_en_cours_edition = donnees.donnees;
            
            document.getElementById('modal-titre').textContent = 'Modifier le produit';
            document.getElementById('id-produit-modif').value = produit_en_cours_edition.id_produit;
            document.getElementById('nom-produit').value = produit_en_cours_edition.nom_produit;
            document.getElementById('categorie-produit').value = produit_en_cours_edition.id_categorie;
            document.getElementById('quantite-totale').value = produit_en_cours_edition.quantite_totale;
            document.getElementById('quantite-disponible').value = produit_en_cours_edition.quantite_disponible;
            document.getElementById('description-produit').value = produit_en_cours_edition.description_produit || '';
            document.getElementById('url-image-produit').value = produit_en_cours_edition.image_url_produit || '';
            
            // Afficher l'image actuelle
            if (produit_en_cours_edition.image_url_produit) {
                document.getElementById('apercu-image').innerHTML = `
                    <img src="${produit_en_cours_edition.image_url_produit}" alt="Aperçu" style="max-width: 100%; max-height: 200px; border-radius: 8px;">
                    <p style="margin-top: 10px;">Cliquez pour changer l'image</p>
                `;
            }
            
            document.getElementById('modal-produit-admin').style.display = 'flex';
        }
    } catch (erreur) {
        console.error('Erreur lors du chargement du produit:', erreur);
        alert('Erreur lors du chargement du produit');
    }
}

/**
 * Ferme le modal produit
 */
function fermerModalProduitAdmin() {
    document.getElementById('modal-produit-admin').style.display = 'none';
    produit_en_cours_edition = null;
}

/**
 * Gère l'upload d'image
 */
async function gererUploadImage(event) {
    const fichier = event.target.files[0];
    if (!fichier) return;
    
    // Vérifier la taille
    if (fichier.size > 5 * 1024 * 1024) {
        alert('Fichier trop volumineux (5 Mo maximum)');
        return;
    }
    
    // Afficher un aperçu temporaire
    const lecteur = new FileReader();
    lecteur.onload = function(e) {
        document.getElementById('apercu-image').innerHTML = `
            <img src="${e.target.result}" alt="Aperçu" style="max-width: 100%; max-height: 200px; border-radius: 8px;">
            <p style="margin-top: 10px;">Upload en cours...</p>
        `;
    };
    lecteur.readAsDataURL(fichier);
    
    // Uploader le fichier
    const form_data = new FormData();
    form_data.append('image', fichier);
    form_data.append('type', 'produit');
    
    try {
        const reponse = await fetch('../../backend/api/api_upload_image.php', {
            method: 'POST',
            body: form_data
        });
        
        const donnees = await reponse.json();
        
        if (donnees.succes && donnees.donnees) {
            document.getElementById('url-image-produit').value = donnees.donnees.url;
            document.getElementById('apercu-image').innerHTML = `
                <img src="${donnees.donnees.url}" alt="Aperçu" style="max-width: 100%; max-height: 200px; border-radius: 8px;">
                <p style="margin-top: 10px; color: green;">✓ Image uploadée</p>
            `;
        } else {
            alert(donnees.message || 'Erreur lors de l\'upload');
            document.getElementById('apercu-image').innerHTML = `
                <p>Erreur lors de l'upload</p>
                <small>Cliquez pour réessayer</small>
            `;
        }
    } catch (erreur) {
        console.error('Erreur upload:', erreur);
        alert('Erreur lors de l\'upload de l\'image');
    }
}

/**
 * Soumet le formulaire produit
 */
async function soumettreFormulaireProduit(event) {
    event.preventDefault();
    
    const bouton_submit = document.getElementById('bouton-submit-produit');
    bouton_submit.disabled = true;
    bouton_submit.textContent = 'Enregistrement...';
    
    const id_produit = document.getElementById('id-produit-modif').value;
    const donnees_produit = {
        nom_produit: document.getElementById('nom-produit').value.trim(),
        id_categorie: parseInt(document.getElementById('categorie-produit').value),
        quantite_totale: parseInt(document.getElementById('quantite-totale').value),
        quantite_disponible: parseInt(document.getElementById('quantite-disponible').value),
        description_produit: document.getElementById('description-produit').value.trim(),
        image_url_produit: document.getElementById('url-image-produit').value
    };
    
    try {
        let reponse;
        
        if (id_produit) {
            // Modification
            donnees_produit.id_produit = parseInt(id_produit);
            reponse = await fetch('../../backend/api/api_produits.php', {
                method: 'PUT',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(donnees_produit)
            });
        } else {
            // Ajout
            reponse = await fetch('../../backend/api/api_produits.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(donnees_produit)
            });
        }
        
        const donnees = await reponse.json();
        
        if (donnees.succes) {
            afficherMessageFormulaire(donnees.message, 'succes');
            
            setTimeout(() => {
                fermerModalProduitAdmin();
                chargerTousLesProduits();
            }, 1500);
        } else {
            afficherMessageFormulaire(donnees.message, 'erreur');
            bouton_submit.disabled = false;
            bouton_submit.textContent = 'Enregistrer';
        }
    } catch (erreur) {
        console.error('Erreur:', erreur);
        afficherMessageFormulaire('Erreur lors de l\'enregistrement', 'erreur');
        bouton_submit.disabled = false;
        bouton_submit.textContent = 'Enregistrer';
    }
}

/**
 * Toggle disponibilité d'un produit
 */
async function toggleDisponibilite(id_produit, est_disponible) {
    try {
        const reponse = await fetch('../../backend/api/api_produits.php', {
            method: 'PUT',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                id_produit: id_produit,
                est_disponible: est_disponible ? 1 : 0
            })
        });
        
        const donnees = await reponse.json();
        
        if (donnees.succes) {
            chargerTousLesProduits();
        } else {
            alert(donnees.message);
        }
    } catch (erreur) {
        console.error('Erreur:', erreur);
        alert('Erreur lors de la mise à jour');
    }
}

/**
 * Confirme et supprime un produit
 */
async function confirmerSuppression(id_produit, nom_produit) {
    if (!confirm(`Êtes-vous sûr de vouloir supprimer "${nom_produit}" ?`)) {
        return;
    }
    
    try {
        const reponse = await fetch(`../../backend/api/api_produits.php?id=${id_produit}`, {
            method: 'DELETE'
        });
        
        const donnees = await reponse.json();
        
        if (donnees.succes) {
            chargerTousLesProduits();
        } else {
            alert(donnees.message);
        }
    } catch (erreur) {
        console.error('Erreur:', erreur);
        alert('Erreur lors de la suppression');
    }
}

/**
 * Filtre les produits
 */
function filtrerProduits() {
    const recherche = document.getElementById('recherche-produits').value.toLowerCase();
    const categorie_filtre = document.getElementById('filtre-categorie').value;
    const disponibilite_filtre = document.getElementById('filtre-disponibilite').value;
    
    let produits_filtres = tous_les_produits;
    
    // Filtre par recherche
    if (recherche) {
        produits_filtres = produits_filtres.filter(p => 
            p.nom_produit.toLowerCase().includes(recherche) ||
            (p.description_produit && p.description_produit.toLowerCase().includes(recherche)) ||
            (p.nom_categorie && p.nom_categorie.toLowerCase().includes(recherche))
        );
    }
    
    // Filtre par catégorie
    if (categorie_filtre) {
        produits_filtres = produits_filtres.filter(p => p.id_categorie == categorie_filtre);
    }
    
    // Filtre par disponibilité
    if (disponibilite_filtre === 'disponible') {
        produits_filtres = produits_filtres.filter(p => p.est_disponible == 1 && p.quantite_disponible > 0);
    } else if (disponibilite_filtre === 'indisponible') {
        produits_filtres = produits_filtres.filter(p => p.est_disponible == 0 || p.quantite_disponible == 0);
    }
    
    afficherTableauProduits(produits_filtres);
}

/**
 * Affiche un message dans le formulaire
 */
function afficherMessageFormulaire(message, type) {
    const zone_message = document.getElementById('message-formulaire-produit');
    zone_message.className = type === 'succes' ? 'message-succes-form' : 'message-erreur-form';
    zone_message.textContent = message;
    zone_message.style.display = 'block';
}

/**
 * Échappe les caractères HTML
 */
function escapeHtml(texte) {
    const div = document.createElement('div');
    div.textContent = texte;
    return div.innerHTML;
}
