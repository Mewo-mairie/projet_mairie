/**
 * Script JavaScript pour la gestion des utilisateurs (admin)
 */

// Variables globales
let tous_les_utilisateurs = [];
let utilisateur_selectionne = null;

document.addEventListener('DOMContentLoaded', function() {
    initialiserPageUtilisateurs();
});

/**
 * Initialise la page de gestion des utilisateurs
 */
async function initialiserPageUtilisateurs() {
    await chargerTousLesUtilisateurs();
    initialiserEvenements();
}

/**
 * Charge tous les utilisateurs
 */
async function chargerTousLesUtilisateurs() {
    try {
        const reponse = await fetch('../../backend/api/api_utilisateurs.php');
        const donnees = await reponse.json();
        
        if (donnees.succes && donnees.donnees) {
            tous_les_utilisateurs = donnees.donnees;
            afficherTableauUtilisateurs(tous_les_utilisateurs);
        }
    } catch (erreur) {
        console.error('Erreur lors du chargement des utilisateurs:', erreur);
    }
}

/**
 * Affiche le tableau des utilisateurs
 */
function afficherTableauUtilisateurs(utilisateurs) {
    const conteneur = document.getElementById('conteneur-tableau-utilisateurs');
    if (!conteneur) return;
    
    if (utilisateurs.length === 0) {
        conteneur.innerHTML = '<p class="message-vide">Aucun utilisateur trouvé</p>';
        return;
    }
    
    let html = `
        <table class="tableau-admin">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nom complet</th>
                    <th>Email</th>
                    <th>Rôle</th>
                    <th>Inscription</th>
                    <th>Statut</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
    `;
    
    utilisateurs.forEach(user => {
        const date = new Date(user.date_inscription);
        const date_formatee = date.toLocaleDateString('fr-FR');
        const badge_role = user.role_utilisateur === 'administrateur' 
            ? '<span class="badge-admin">Admin</span>' 
            : '<span class="badge-user">Utilisateur</span>';
        
        html += `
            <tr>
                <td><strong>#${user.id_utilisateur}</strong></td>
                <td>${escapeHtml(user.prenom_utilisateur)} ${escapeHtml(user.nom_utilisateur)}</td>
                <td>${escapeHtml(user.email_utilisateur)}</td>
                <td>
                    <select 
                        class="select-role-inline" 
                        onchange="changerRole(${user.id_utilisateur}, this.value)"
                        data-role-actuel="${user.role_utilisateur}"
                    >
                        <option value="utilisateur" ${user.role_utilisateur === 'utilisateur' ? 'selected' : ''}>Utilisateur</option>
                        <option value="administrateur" ${user.role_utilisateur === 'administrateur' ? 'selected' : ''}>Administrateur</option>
                    </select>
                </td>
                <td>${date_formatee}</td>
                <td>
                    <label class="switch-disponibilite">
                        <input 
                            type="checkbox" 
                            ${user.est_actif == 1 ? 'checked' : ''}
                            onchange="toggleStatutUtilisateur(${user.id_utilisateur}, this.checked)"
                        >
                        <span class="slider-disponibilite"></span>
                    </label>
                    <span class="label-statut">${user.est_actif == 1 ? 'Actif' : 'Inactif'}</span>
                </td>
                <td class="actions-tableau">
                    <button 
                        class="bouton-action-petit bouton-modifier" 
                        onclick="afficherDetails(${user.id_utilisateur})"
                        title="Voir détails"
                    >
                        👁️
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
    // Recherche
    const champ_recherche = document.getElementById('recherche-utilisateurs');
    if (champ_recherche) {
        champ_recherche.addEventListener('input', filtrerUtilisateurs);
    }
    
    // Filtres
    const filtre_role = document.getElementById('filtre-role');
    const filtre_statut = document.getElementById('filtre-statut');
    
    if (filtre_role) {
        filtre_role.addEventListener('change', filtrerUtilisateurs);
    }
    
    if (filtre_statut) {
        filtre_statut.addEventListener('change', filtrerUtilisateurs);
    }
}

/**
 * Change le rôle d'un utilisateur
 */
async function changerRole(id_utilisateur, nouveau_role) {
    const select = event.target;
    const ancien_role = select.dataset.roleActuel;
    
    if (ancien_role === nouveau_role) return;
    
    if (!confirm(`Changer le rôle de cet utilisateur en "${nouveau_role}" ?`)) {
        select.value = ancien_role;
        return;
    }
    
    try {
        const reponse = await fetch('../../backend/api/api_utilisateurs.php', {
            method: 'PUT',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                id_utilisateur: id_utilisateur,
                role_utilisateur: nouveau_role
            })
        });
        
        const donnees = await reponse.json();
        
        if (donnees.succes) {
            select.dataset.roleActuel = nouveau_role;
            await chargerTousLesUtilisateurs();
        } else {
            alert(donnees.message);
            select.value = ancien_role;
        }
    } catch (erreur) {
        console.error('Erreur:', erreur);
        alert('Erreur lors du changement de rôle');
        select.value = ancien_role;
    }
}

/**
 * Toggle le statut actif/inactif d'un utilisateur
 */
async function toggleStatutUtilisateur(id_utilisateur, est_actif) {
    try {
        const reponse = await fetch('../../backend/api/api_utilisateurs.php', {
            method: 'PUT',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                id_utilisateur: id_utilisateur,
                est_actif: est_actif ? 1 : 0
            })
        });
        
        const donnees = await reponse.json();
        
        if (donnees.succes) {
            await chargerTousLesUtilisateurs();
        } else {
            alert(donnees.message);
        }
    } catch (erreur) {
        console.error('Erreur:', erreur);
        alert('Erreur lors de la modification du statut');
    }
}

/**
 * Affiche les détails d'un utilisateur
 */
async function afficherDetails(id_utilisateur) {
    try {
        const reponse = await fetch(`../../backend/api/api_utilisateurs.php?id=${id_utilisateur}`);
        const donnees = await reponse.json();
        
        if (donnees.succes && donnees.donnees) {
            utilisateur_selectionne = donnees.donnees;
            afficherModalDetails(utilisateur_selectionne);
        }
    } catch (erreur) {
        console.error('Erreur:', erreur);
        alert('Erreur lors du chargement des détails');
    }
}

/**
 * Affiche le modal de détails
 */
function afficherModalDetails(user) {
    const conteneur = document.getElementById('contenu-details-utilisateur');
    
    const date_inscription = new Date(user.date_inscription).toLocaleDateString('fr-FR');
    const stats = user.statistiques;
    
    conteneur.innerHTML = `
        <div class="info-groupe">
            <h3>👤 Informations générales</h3>
            <div class="info-ligne">
                <span class="info-label">ID :</span>
                <span class="info-valeur">#${user.id_utilisateur}</span>
            </div>
            <div class="info-ligne">
                <span class="info-label">Nom complet :</span>
                <span class="info-valeur">${escapeHtml(user.prenom_utilisateur)} ${escapeHtml(user.nom_utilisateur)}</span>
            </div>
            <div class="info-ligne">
                <span class="info-label">Email :</span>
                <span class="info-valeur">${escapeHtml(user.email_utilisateur)}</span>
            </div>
            <div class="info-ligne">
                <span class="info-label">Rôle :</span>
                <span class="info-valeur">${user.role_utilisateur === 'administrateur' ? '👑 Administrateur' : '👤 Utilisateur'}</span>
            </div>
            <div class="info-ligne">
                <span class="info-label">Date d'inscription :</span>
                <span class="info-valeur">${date_inscription}</span>
            </div>
            <div class="info-ligne">
                <span class="info-label">Statut :</span>
                <span class="info-valeur">${user.est_actif == 1 ? '✅ Actif' : '❌ Inactif'}</span>
            </div>
        </div>
        
        ${stats ? `
        <div class="info-groupe">
            <h3>📊 Statistiques de réservations</h3>
            <div class="info-ligne">
                <span class="info-label">Total réservations :</span>
                <span class="info-valeur">${stats.total_reservations}</span>
            </div>
            <div class="info-ligne">
                <span class="info-label">En attente :</span>
                <span class="info-valeur">${stats.en_attente}</span>
            </div>
            <div class="info-ligne">
                <span class="info-label">Confirmées :</span>
                <span class="info-valeur">${stats.confirmees}</span>
            </div>
            <div class="info-ligne">
                <span class="info-label">En cours :</span>
                <span class="info-valeur">${stats.en_cours}</span>
            </div>
            <div class="info-ligne">
                <span class="info-label">Rendues :</span>
                <span class="info-valeur">${stats.rendues}</span>
            </div>
            <div class="info-ligne">
                <span class="info-label">Annulées :</span>
                <span class="info-valeur">${stats.annulees}</span>
            </div>
        </div>
        ` : ''}
    `;
    
    document.getElementById('modal-details-utilisateur').style.display = 'flex';
}

/**
 * Ferme le modal de détails
 */
function fermerModalDetails() {
    document.getElementById('modal-details-utilisateur').style.display = 'none';
    utilisateur_selectionne = null;
}

/**
 * Filtre les utilisateurs
 */
function filtrerUtilisateurs() {
    const recherche = document.getElementById('recherche-utilisateurs').value.toLowerCase();
    const role_filtre = document.getElementById('filtre-role').value;
    const statut_filtre = document.getElementById('filtre-statut').value;
    
    let utilisateurs_filtres = tous_les_utilisateurs;
    
    // Filtre par recherche
    if (recherche) {
        utilisateurs_filtres = utilisateurs_filtres.filter(u => 
            u.prenom_utilisateur.toLowerCase().includes(recherche) ||
            u.nom_utilisateur.toLowerCase().includes(recherche) ||
            u.email_utilisateur.toLowerCase().includes(recherche) ||
            u.id_utilisateur.toString().includes(recherche)
        );
    }
    
    // Filtre par rôle
    if (role_filtre) {
        utilisateurs_filtres = utilisateurs_filtres.filter(u => u.role_utilisateur === role_filtre);
    }
    
    // Filtre par statut
    if (statut_filtre === 'actif') {
        utilisateurs_filtres = utilisateurs_filtres.filter(u => u.est_actif == 1);
    } else if (statut_filtre === 'inactif') {
        utilisateurs_filtres = utilisateurs_filtres.filter(u => u.est_actif == 0);
    }
    
    afficherTableauUtilisateurs(utilisateurs_filtres);
}

/**
 * Échappe les caractères HTML
 */
function escapeHtml(texte) {
    const div = document.createElement('div');
    div.textContent = texte;
    return div.innerHTML;
}
