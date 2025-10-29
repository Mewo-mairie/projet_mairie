/**
 * Script JavaScript pour la visualisation des logs admin
 */

// Variables globales
let page_actuelle = 1;
let total_logs = 0;
let logs_par_page = 50;

document.addEventListener('DOMContentLoaded', function() {
    initialiserPageLogs();
});

/**
 * Initialise la page des logs
 */
async function initialiserPageLogs() {
    await chargerLogs(page_actuelle);
    initialiserEvenements();
}

/**
 * Charge les logs avec pagination
 */
async function chargerLogs(page) {
    try {
        const reponse = await fetch(`../../backend/api/api_logs.php?page=${page}&limite=${logs_par_page}`);
        const donnees = await reponse.json();
        
        if (donnees.succes && donnees.donnees) {
            const logs = donnees.donnees.logs;
            const pagination = donnees.donnees.pagination;
            
            total_logs = pagination.total;
            page_actuelle = pagination.page_actuelle;
            
            afficherTableauLogs(logs);
            afficherPagination(pagination);
            afficherInfoTotal();
        }
    } catch (erreur) {
        console.error('Erreur lors du chargement des logs:', erreur);
    }
}

/**
 * Affiche le tableau des logs
 */
function afficherTableauLogs(logs) {
    const conteneur = document.getElementById('conteneur-tableau-logs');
    if (!conteneur) return;
    
    if (logs.length === 0) {
        conteneur.innerHTML = '<p class="message-vide">Aucun log trouvé</p>';
        return;
    }
    
    let html = `
        <table class="tableau-admin tableau-logs">
            <thead>
                <tr>
                    <th>Date/Heure</th>
                    <th>Administrateur</th>
                    <th>Action</th>
                    <th>Détails</th>
                    <th>IP</th>
                </tr>
            </thead>
            <tbody>
    `;
    
    logs.forEach(log => {
        const date = new Date(log.date_action);
        const date_formatee = date.toLocaleString('fr-FR');
        const admin_nom = log.prenom_utilisateur 
            ? `${log.prenom_utilisateur} ${log.nom_utilisateur}` 
            : 'Admin supprimé';
        
        html += `
            <tr>
                <td class="col-date">${date_formatee}</td>
                <td class="col-admin">
                    ${escapeHtml(admin_nom)}
                    ${log.email_utilisateur ? `<br><small>${escapeHtml(log.email_utilisateur)}</small>` : ''}
                </td>
                <td class="col-action">
                    <span class="badge-action">${formaterAction(log.action_admin)}</span>
                </td>
                <td class="col-details">${escapeHtml(log.details_action)}</td>
                <td class="col-ip"><code>${log.adresse_ip}</code></td>
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
 * Affiche la pagination
 */
function afficherPagination(pagination) {
    const conteneur = document.getElementById('pagination-logs');
    if (!conteneur) return;
    
    if (pagination.total_pages <= 1) {
        conteneur.innerHTML = '';
        return;
    }
    
    let html = '<div class="pagination-boutons">';
    
    // Bouton précédent
    if (pagination.page_actuelle > 1) {
        html += `<button class="bouton-pagination" onclick="chargerLogs(${pagination.page_actuelle - 1})">← Précédent</button>`;
    }
    
    // Pages
    const pages_a_afficher = calculerPagesAffichees(pagination.page_actuelle, pagination.total_pages);
    
    pages_a_afficher.forEach(page => {
        if (page === '...') {
            html += '<span class="pagination-points">...</span>';
        } else {
            const classe_active = page === pagination.page_actuelle ? 'actif' : '';
            html += `<button class="bouton-pagination ${classe_active}" onclick="chargerLogs(${page})">${page}</button>`;
        }
    });
    
    // Bouton suivant
    if (pagination.page_actuelle < pagination.total_pages) {
        html += `<button class="bouton-pagination" onclick="chargerLogs(${pagination.page_actuelle + 1})">Suivant →</button>`;
    }
    
    html += '</div>';
    conteneur.innerHTML = html;
}

/**
 * Calcule les pages à afficher dans la pagination
 */
function calculerPagesAffichees(page_actuelle, total_pages) {
    const pages = [];
    const max_pages_visibles = 7;
    
    if (total_pages <= max_pages_visibles) {
        for (let i = 1; i <= total_pages; i++) {
            pages.push(i);
        }
    } else {
        pages.push(1);
        
        if (page_actuelle > 3) {
            pages.push('...');
        }
        
        const debut = Math.max(2, page_actuelle - 1);
        const fin = Math.min(total_pages - 1, page_actuelle + 1);
        
        for (let i = debut; i <= fin; i++) {
            pages.push(i);
        }
        
        if (page_actuelle < total_pages - 2) {
            pages.push('...');
        }
        
        pages.push(total_pages);
    }
    
    return pages;
}

/**
 * Affiche le total de logs
 */
function afficherInfoTotal() {
    const info_total = document.getElementById('info-total');
    if (info_total) {
        info_total.textContent = total_logs;
    }
}

/**
 * Initialise les événements
 */
function initialiserEvenements() {
    // Recherche
    const champ_recherche = document.getElementById('recherche-logs');
    if (champ_recherche) {
        let timer_recherche;
        champ_recherche.addEventListener('input', function() {
            clearTimeout(timer_recherche);
            const terme = this.value.trim();
            
            timer_recherche = setTimeout(function() {
                if (terme) {
                    rechercherLogs(terme);
                } else {
                    chargerLogs(1);
                }
            }, 500);
        });
    }
}

/**
 * Recherche dans les logs
 */
async function rechercherLogs(terme) {
    try {
        const reponse = await fetch(`../../backend/api/api_logs.php?recherche=${encodeURIComponent(terme)}&limite=100`);
        const donnees = await reponse.json();
        
        if (donnees.succes && donnees.donnees) {
            // La recherche retourne directement les logs sans pagination
            afficherTableauLogs(donnees.donnees);
            document.getElementById('pagination-logs').innerHTML = '';
            document.getElementById('info-total').textContent = donnees.donnees.length;
        }
    } catch (erreur) {
        console.error('Erreur lors de la recherche:', erreur);
    }
}

/**
 * Formate une action en texte lisible
 */
function formaterAction(action) {
    const actions = {
        'PRODUIT_CREE': '📦 Produit créé',
        'PRODUIT_MODIFIE': '✏️ Produit modifié',
        'PRODUIT_SUPPRIME': '🗑️ Produit supprimé',
        'PRODUIT_DISPONIBILITE_MODIFIEE': '🔄 Disponibilité modifiée',
        'CATEGORIE_CREEE': '🏷️ Catégorie créée',
        'CATEGORIE_MODIFIEE': '✏️ Catégorie modifiée',
        'CATEGORIE_SUPPRIMEE': '🗑️ Catégorie supprimée',
        'RESERVATION_STATUT_MODIFIE': '📅 Statut réservation modifié',
        'RESERVATION_CONFIRMEE': '✅ Réservation confirmée',
        'RESERVATION_RECUPEREE': '📥 Matériel récupéré',
        'RESERVATION_RENDUE': '📤 Matériel rendu',
        'RESERVATION_ANNULEE': '❌ Réservation annulée',
        'UTILISATEUR_ROLE_MODIFIE': '👥 Rôle utilisateur modifié',
        'UTILISATEUR_ACTIVE': '✅ Utilisateur activé',
        'UTILISATEUR_DESACTIVE': '⛔ Utilisateur désactivé',
        'CONNEXION_ADMIN': '🔑 Connexion admin',
        'DECONNEXION_ADMIN': '🚪 Déconnexion admin',
        'PARAMETRES_MODIFIES': '⚙️ Paramètres modifiés'
    };
    
    return actions[action] || action;
}

/**
 * Échappe les caractères HTML
 */
function escapeHtml(texte) {
    if (!texte) return '';
    const div = document.createElement('div');
    div.textContent = texte;
    return div.innerHTML;
}
