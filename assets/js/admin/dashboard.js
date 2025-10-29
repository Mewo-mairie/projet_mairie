/**
 * Script JavaScript pour le dashboard administrateur
 * Charge et affiche les statistiques
 */

document.addEventListener('DOMContentLoaded', function() {
    initialiserDashboard();
});

/**
 * Initialise le dashboard
 */
async function initialiserDashboard() {
    // Afficher la date actuelle
    afficherDateActuelle();
    
    // Charger les statistiques
    await chargerStatistiquesGenerales();
    await chargerDernieresReservations();
    await chargerProduitsPlusReserves();
    await chargerStatistiquesMensuelles();
}

/**
 * Affiche la date actuelle
 */
function afficherDateActuelle() {
    const element_date = document.getElementById('date-actuelle');
    if (!element_date) return;
    
    const options = { 
        weekday: 'long', 
        year: 'numeric', 
        month: 'long', 
        day: 'numeric' 
    };
    
    const date = new Date().toLocaleDateString('fr-FR', options);
    element_date.textContent = date.charAt(0).toUpperCase() + date.slice(1);
}

/**
 * Charge les statistiques générales
 */
async function chargerStatistiquesGenerales() {
    try {
        const reponse = await fetch('../../backend/api/api_statistiques.php?type=generales');
        const donnees = await reponse.json();
        
        if (donnees.succes && donnees.donnees) {
            afficherStatistiquesGenerales(donnees.donnees);
        }
    } catch (erreur) {
        console.error('Erreur lors du chargement des statistiques:', erreur);
    }
}

/**
 * Affiche les statistiques générales
 */
function afficherStatistiquesGenerales(stats) {
    const grille = document.querySelector('.grille-stats');
    if (!grille) return;
    
    grille.innerHTML = `
        <div class="carte-stat bleu">
            <span class="carte-stat-icone">📦</span>
            <span class="carte-stat-label">Produits disponibles</span>
            <p class="carte-stat-valeur">${stats.nombre_produits_disponibles}</p>
            <span class="carte-stat-variation">sur ${stats.nombre_produits_total} total</span>
        </div>
        
        <div class="carte-stat vert">
            <span class="carte-stat-icone">📅</span>
            <span class="carte-stat-label">Réservations ce mois</span>
            <p class="carte-stat-valeur">${stats.reservations_ce_mois}</p>
            <span class="carte-stat-variation">sur ${stats.nombre_reservations_total} total</span>
        </div>
        
        <div class="carte-stat orange">
            <span class="carte-stat-icone">⏳</span>
            <span class="carte-stat-label">En attente</span>
            <p class="carte-stat-valeur">${stats.reservations_en_attente}</p>
            <span class="carte-stat-variation">à traiter</span>
        </div>
        
        <div class="carte-stat violet">
            <span class="carte-stat-icone">👥</span>
            <span class="carte-stat-label">Utilisateurs actifs</span>
            <p class="carte-stat-valeur">${stats.utilisateurs_actifs_mois}</p>
            <span class="carte-stat-variation">ce mois</span>
        </div>
        
        <div class="carte-stat rouge">
            <span class="carte-stat-icone">🔄</span>
            <span class="carte-stat-label">En cours</span>
            <p class="carte-stat-valeur">${stats.reservations_en_cours}</p>
            <span class="carte-stat-variation">récupérées</span>
        </div>
        
        <div class="carte-stat bleu">
            <span class="carte-stat-icone">📊</span>
            <span class="carte-stat-label">Taux d'utilisation</span>
            <p class="carte-stat-valeur">${stats.taux_utilisation}%</p>
            <span class="carte-stat-variation">des produits</span>
        </div>
        
        <div class="carte-stat vert">
            <span class="carte-stat-icone">✅</span>
            <span class="carte-stat-label">Confirmées</span>
            <p class="carte-stat-valeur">${stats.reservations_confirmees}</p>
            <span class="carte-stat-variation">validées</span>
        </div>
        
        <div class="carte-stat orange">
            <span class="carte-stat-icone">🏷️</span>
            <span class="carte-stat-label">Catégories</span>
            <p class="carte-stat-valeur">${stats.nombre_categories_total}</p>
            <span class="carte-stat-variation">actives</span>
        </div>
    `;
}

/**
 * Charge les dernières réservations
 */
async function chargerDernieresReservations() {
    try {
        const reponse = await fetch('../../backend/api/api_statistiques.php?type=dernieres-reservations&limite=5');
        const donnees = await reponse.json();
        
        if (donnees.succes && donnees.donnees) {
            afficherDernieresReservations(donnees.donnees);
        }
    } catch (erreur) {
        console.error('Erreur lors du chargement des réservations:', erreur);
    }
}

/**
 * Affiche les dernières réservations
 */
function afficherDernieresReservations(reservations) {
    const conteneur = document.getElementById('conteneur-reservations-attente');
    if (!conteneur) return;
    
    if (reservations.length === 0) {
        conteneur.innerHTML = '<p class="message-vide">Aucune réservation récente</p>';
        return;
    }
    
    let html = `
        <table class="tableau-admin">
            <thead>
                <tr>
                    <th>Produit</th>
                    <th>Utilisateur</th>
                    <th>Quantité</th>
                    <th>Date</th>
                    <th>Statut</th>
                </tr>
            </thead>
            <tbody>
    `;
    
    reservations.forEach(reservation => {
        const date = new Date(reservation.date_reservation);
        const date_formatee = date.toLocaleDateString('fr-FR');
        
        html += `
            <tr>
                <td><strong>${escapeHtml(reservation.nom_produit)}</strong></td>
                <td>${escapeHtml(reservation.prenom_utilisateur)} ${escapeHtml(reservation.nom_utilisateur)}</td>
                <td>${reservation.quantite_reservee}</td>
                <td>${date_formatee}</td>
                <td><span class="badge-statut badge-${reservation.statut_reservation}">${formaterStatut(reservation.statut_reservation)}</span></td>
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
 * Charge les produits les plus réservés
 */
async function chargerProduitsPlusReserves() {
    try {
        const reponse = await fetch('../../backend/api/api_statistiques.php?type=produits-populaires&limite=5');
        const donnees = await reponse.json();
        
        if (donnees.succes && donnees.donnees) {
            afficherProduitsPlusReserves(donnees.donnees);
        }
    } catch (erreur) {
        console.error('Erreur lors du chargement des produits populaires:', erreur);
    }
}

/**
 * Affiche les produits les plus réservés
 */
function afficherProduitsPlusReserves(produits) {
    const conteneur = document.getElementById('conteneur-produits-populaires');
    if (!conteneur) return;
    
    if (produits.length === 0) {
        conteneur.innerHTML = '<p class="message-vide">Aucune donnée disponible</p>';
        return;
    }
    
    let html = '';
    
    produits.forEach(produit => {
        const image_url = produit.image_url_produit || '../../assets/images/default-product.png';
        
        html += `
            <div class="carte-produit-populaire">
                <img src="${image_url}" alt="${escapeHtml(produit.nom_produit)}" onerror="this.src='../../assets/images/default-product.png'">
                <h4>${escapeHtml(produit.nom_produit)}</h4>
                <p>${escapeHtml(produit.nom_categorie)}</p>
                <span class="nombre-reservations">${produit.nombre_reservations} réservations</span>
            </div>
        `;
    });
    
    conteneur.innerHTML = html;
}

/**
 * Charge les statistiques mensuelles
 */
async function chargerStatistiquesMensuelles() {
    try {
        const reponse = await fetch('../../backend/api/api_statistiques.php?type=mensuelles');
        const donnees = await reponse.json();
        
        if (donnees.succes && donnees.donnees) {
            afficherGraphiqueMensuel(donnees.donnees);
        }
    } catch (erreur) {
        console.error('Erreur lors du chargement des statistiques mensuelles:', erreur);
    }
}

/**
 * Affiche le graphique mensuel
 */
function afficherGraphiqueMensuel(donnees) {
    const conteneur = document.getElementById('conteneur-graphique-mensuel');
    if (!conteneur) return;
    
    if (donnees.length === 0) {
        conteneur.innerHTML = '<p class="message-vide">Aucune donnée disponible</p>';
        return;
    }
    
    // Trouver la valeur maximale pour la mise à l'échelle
    const max = Math.max(...donnees.map(d => d.nombre_reservations));
    
    let html = '<div class="graphique-barres">';
    
    donnees.forEach(item => {
        const hauteur_pourcent = max > 0 ? (item.nombre_reservations / max) * 100 : 0;
        const mois_formate = formaterMois(item.mois);
        
        html += `
            <div class="barre-graphique" style="height: ${hauteur_pourcent}%">
                <span class="barre-valeur">${item.nombre_reservations}</span>
                <span class="barre-label">${mois_formate}</span>
            </div>
        `;
    });
    
    html += '</div>';
    
    conteneur.innerHTML = html;
}

/**
 * Formate un mois au format YYYY-MM en texte court
 */
function formaterMois(mois_string) {
    const [annee, mois] = mois_string.split('-');
    const mois_noms = ['Jan', 'Fév', 'Mar', 'Avr', 'Mai', 'Jun', 'Jul', 'Aoû', 'Sep', 'Oct', 'Nov', 'Déc'];
    const index = parseInt(mois) - 1;
    return mois_noms[index];
}

/**
 * Formate un statut de réservation
 */
function formaterStatut(statut) {
    const statuts = {
        'en_attente': 'En attente',
        'confirmee': 'Confirmée',
        'recuperee': 'Récupérée',
        'rendue': 'Rendue',
        'annulee': 'Annulée'
    };
    return statuts[statut] || statut;
}

/**
 * Échappe les caractères HTML pour éviter les injections XSS
 */
function escapeHtml(texte) {
    const div = document.createElement('div');
    div.textContent = texte;
    return div.innerHTML;
}
