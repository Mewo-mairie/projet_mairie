/**
 * Script JavaScript pour la gestion des catégories (admin)
 */

// Variables globales
let toutes_les_categories = [];
let categorie_en_cours_edition = null;

document.addEventListener('DOMContentLoaded', function() {
    initialiserPageCategories();
});

/**
 * Initialise la page de gestion des catégories
 */
async function initialiserPageCategories() {
    await chargerToutesLesCategories();
    initialiserEvenements();
}

/**
 * Charge toutes les catégories
 */
async function chargerToutesLesCategories() {
    try {
        const reponse = await fetch('../../backend/api/api_categories.php');
        const donnees = await reponse.json();
        
        if (donnees.succes && donnees.donnees) {
            toutes_les_categories = donnees.donnees;
            afficherGrilleCategories(toutes_les_categories);
        }
    } catch (erreur) {
        console.error('Erreur lors du chargement des catégories:', erreur);
        afficherMessage('Erreur lors du chargement', 'erreur');
    }
}

/**
 * Affiche la grille des catégories
 */
function afficherGrilleCategories(categories) {
    const conteneur = document.getElementById('conteneur-grille-categories');
    if (!conteneur) return;
    
    if (categories.length === 0) {
        conteneur.innerHTML = '<p class="message-vide">Aucune catégorie</p>';
        return;
    }
    
    let html = '<div class="grille-categories-admin">';
    
    categories.forEach(categorie => {
        const image_url = categorie.image_url_categorie || '../../assets/images/default-category.png';
        
        html += `
            <div class="carte-categorie-admin">
                <div class="image-categorie-admin">
                    <img src="${image_url}" alt="${escapeHtml(categorie.nom_categorie)}" onerror="this.src='../../assets/images/default-category.png'">
                </div>
                <div class="infos-categorie-admin">
                    <h3>${escapeHtml(categorie.nom_categorie)}</h3>
                    <p>${escapeHtml(categorie.description_categorie || 'Aucune description')}</p>
                    <div class="stats-categorie">
                        <span class="badge-info">📅 ${formaterDate(categorie.date_creation)}</span>
                    </div>
                </div>
                <div class="actions-categorie-admin">
                    <button 
                        class="bouton-action bouton-secondaire bouton-petit-admin" 
                        onclick="ouvrirModalModification(${categorie.id_categorie})"
                        title="Modifier"
                    >
                        ✏️ Modifier
                    </button>
                    <button 
                        class="bouton-action bouton-danger bouton-petit-admin" 
                        onclick="confirmerSuppression(${categorie.id_categorie}, '${escapeHtml(categorie.nom_categorie)}')"
                        title="Supprimer"
                    >
                        🗑️ Supprimer
                    </button>
                </div>
            </div>
        `;
    });
    
    html += '</div>';
    
    conteneur.innerHTML = html;
}

/**
 * Initialise les événements
 */
function initialiserEvenements() {
    // Bouton ajouter catégorie
    const bouton_ajouter = document.getElementById('bouton-ajouter-categorie');
    if (bouton_ajouter) {
        bouton_ajouter.addEventListener('click', ouvrirModalAjout);
    }
    
    // Formulaire catégorie
    const formulaire = document.getElementById('formulaire-categorie-admin');
    if (formulaire) {
        formulaire.addEventListener('submit', soumettreFormulaireCategorie);
    }
    
    // Upload d'image
    const zone_apercu = document.getElementById('apercu-image-categorie');
    const input_image = document.getElementById('input-image-categorie');
    
    if (zone_apercu && input_image) {
        zone_apercu.addEventListener('click', () => input_image.click());
        input_image.addEventListener('change', gererUploadImage);
    }
}

/**
 * Ouvre le modal pour ajouter une catégorie
 */
function ouvrirModalAjout() {
    categorie_en_cours_edition = null;
    
    document.getElementById('modal-titre').textContent = 'Ajouter une catégorie';
    document.getElementById('formulaire-categorie-admin').reset();
    document.getElementById('id-categorie-modif').value = '';
    document.getElementById('url-image-categorie').value = '';
    document.getElementById('apercu-image-categorie').innerHTML = `
        <p>Cliquez pour sélectionner une image</p>
        <small>JPEG, PNG, GIF, WEBP - Max 5 Mo</small>
    `;
    
    document.getElementById('modal-categorie-admin').style.display = 'flex';
}

/**
 * Ouvre le modal pour modifier une catégorie
 */
async function ouvrirModalModification(id_categorie) {
    try {
        const reponse = await fetch(`../../backend/api/api_categories.php?id=${id_categorie}`);
        const donnees = await reponse.json();
        
        if (donnees.succes && donnees.donnees) {
            categorie_en_cours_edition = donnees.donnees;
            
            document.getElementById('modal-titre').textContent = 'Modifier la catégorie';
            document.getElementById('id-categorie-modif').value = categorie_en_cours_edition.id_categorie;
            document.getElementById('nom-categorie').value = categorie_en_cours_edition.nom_categorie;
            document.getElementById('description-categorie').value = categorie_en_cours_edition.description_categorie || '';
            document.getElementById('url-image-categorie').value = categorie_en_cours_edition.image_url_categorie || '';
            
            // Afficher l'image actuelle
            if (categorie_en_cours_edition.image_url_categorie) {
                document.getElementById('apercu-image-categorie').innerHTML = `
                    <img src="${categorie_en_cours_edition.image_url_categorie}" alt="Aperçu" style="max-width: 100%; max-height: 200px; border-radius: 8px;">
                    <p style="margin-top: 10px;">Cliquez pour changer l'image</p>
                `;
            }
            
            document.getElementById('modal-categorie-admin').style.display = 'flex';
        }
    } catch (erreur) {
        console.error('Erreur lors du chargement de la catégorie:', erreur);
        alert('Erreur lors du chargement de la catégorie');
    }
}

/**
 * Ferme le modal catégorie
 */
function fermerModalCategorieAdmin() {
    document.getElementById('modal-categorie-admin').style.display = 'none';
    categorie_en_cours_edition = null;
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
        document.getElementById('apercu-image-categorie').innerHTML = `
            <img src="${e.target.result}" alt="Aperçu" style="max-width: 100%; max-height: 200px; border-radius: 8px;">
            <p style="margin-top: 10px;">Upload en cours...</p>
        `;
    };
    lecteur.readAsDataURL(fichier);
    
    // Uploader le fichier
    const form_data = new FormData();
    form_data.append('image', fichier);
    form_data.append('type', 'categorie');
    
    try {
        const reponse = await fetch('../../backend/api/api_upload_image.php', {
            method: 'POST',
            body: form_data
        });
        
        const donnees = await reponse.json();
        
        if (donnees.succes && donnees.donnees) {
            document.getElementById('url-image-categorie').value = donnees.donnees.url;
            document.getElementById('apercu-image-categorie').innerHTML = `
                <img src="${donnees.donnees.url}" alt="Aperçu" style="max-width: 100%; max-height: 200px; border-radius: 8px;">
                <p style="margin-top: 10px; color: green;">✓ Image uploadée</p>
            `;
        } else {
            alert(donnees.message || 'Erreur lors de l\'upload');
            document.getElementById('apercu-image-categorie').innerHTML = `
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
 * Soumet le formulaire catégorie
 */
async function soumettreFormulaireCategorie(event) {
    event.preventDefault();
    
    const bouton_submit = document.getElementById('bouton-submit-categorie');
    bouton_submit.disabled = true;
    bouton_submit.textContent = 'Enregistrement...';
    
    const id_categorie = document.getElementById('id-categorie-modif').value;
    const donnees_categorie = {
        nom_categorie: document.getElementById('nom-categorie').value.trim(),
        description_categorie: document.getElementById('description-categorie').value.trim(),
        image_url_categorie: document.getElementById('url-image-categorie').value
    };
    
    try {
        let reponse;
        
        if (id_categorie) {
            // Modification
            donnees_categorie.id_categorie = parseInt(id_categorie);
            reponse = await fetch('../../backend/api/api_categories.php', {
                method: 'PUT',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(donnees_categorie)
            });
        } else {
            // Ajout
            reponse = await fetch('../../backend/api/api_categories.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(donnees_categorie)
            });
        }
        
        const donnees = await reponse.json();
        
        if (donnees.succes) {
            afficherMessageFormulaire(donnees.message, 'succes');
            
            setTimeout(() => {
                fermerModalCategorieAdmin();
                chargerToutesLesCategories();
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
 * Confirme et supprime une catégorie
 */
async function confirmerSuppression(id_categorie, nom_categorie) {
    if (!confirm(`Êtes-vous sûr de vouloir supprimer la catégorie "${nom_categorie}" ?\n\nATTENTION : Impossible si elle contient des produits.`)) {
        return;
    }
    
    try {
        const reponse = await fetch(`../../backend/api/api_categories.php?id=${id_categorie}`, {
            method: 'DELETE'
        });
        
        const donnees = await reponse.json();
        
        if (donnees.succes) {
            chargerToutesLesCategories();
        } else {
            alert(donnees.message);
        }
    } catch (erreur) {
        console.error('Erreur:', erreur);
        alert('Erreur lors de la suppression');
    }
}

/**
 * Affiche un message dans le formulaire
 */
function afficherMessageFormulaire(message, type) {
    const zone_message = document.getElementById('message-formulaire-categorie');
    zone_message.className = type === 'succes' ? 'message-succes-form' : 'message-erreur-form';
    zone_message.textContent = message;
    zone_message.style.display = 'block';
}

/**
 * Formate une date
 */
function formaterDate(date_string) {
    const date = new Date(date_string);
    return date.toLocaleDateString('fr-FR', { 
        year: 'numeric', 
        month: 'short', 
        day: 'numeric' 
    });
}

/**
 * Échappe les caractères HTML
 */
function escapeHtml(texte) {
    const div = document.createElement('div');
    div.textContent = texte;
    return div.innerHTML;
}
