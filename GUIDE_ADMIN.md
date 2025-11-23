# Guide de la Partie Administrateur - Lend&Share

## Vue d'ensemble

La partie administrateur de Lend&Share est maintenant complètement fonctionnelle avec toutes les opérations CRUD (Create, Read, Update, Delete) pour la gestion des produits.

## Modifications Apportées

### 1. Correction des Variables de Session

**Fichiers modifiés :**
- `backend/api/api_connexion.php`
- `backend/api/api_verifier_session.php`

**Changements :**
- Variables de session unifiées : `$_SESSION['utilisateur_connecte']` et `$_SESSION['role_utilisateur']`
- Cohérence avec les vérifications dans les pages admin PHP

### 2. Middleware d'Authentification

**Nouveau fichier :**
- `backend/middleware/middleware_authentification.php`

**Fonctions disponibles :**
- `verifierAuthentificationAdmin()` - Vérifie les permissions administrateur
- `verifierAuthentificationUtilisateur()` - Vérifie qu'un utilisateur est connecté
- `obtenirUtilisateurConnecte()` - Retourne l'ID de l'utilisateur connecté

### 3. API Produits (CRUD Complet)

**Fichier modifié :**
- `backend/api/api_produits.php`

**Endpoints disponibles :**

#### GET - Lister les produits (Public)
- `GET /backend/api/api_produits.php` - Tous les produits
- `GET /backend/api/api_produits.php?id=X` - Un produit spécifique
- `GET /backend/api/api_produits.php?categorie=X` - Produits par catégorie
- `GET /backend/api/api_produits.php?vedettes=1` - Produits en vedette
- `GET /backend/api/api_produits.php?ids=1,2,3` - Plusieurs produits par IDs

#### POST - Créer un produit (Admin uniquement)
```json
{
  "nom_produit": "Nom du produit",
  "description_produit": "Description",
  "id_categorie": 1,
  "image_url_produit": "uploads/products/image.jpg",
  "est_vedette": 0,
  "quantite_totale": 5,
  "quantite_disponible": 5
}
```

**Validations :**
- Nom du produit requis
- Catégorie requise et doit exister
- Vérification de l'authentification admin

**Réponse :**
```json
{
  "success": true,
  "message": "Produit créé avec succès",
  "produit": { ... }
}
```

#### PUT - Modifier un produit (Admin uniquement)
```json
{
  "id_produit": 1,
  "nom_produit": "Nouveau nom",
  "description_produit": "Nouvelle description",
  "id_categorie": 2,
  "quantite_totale": 10,
  "quantite_disponible": 8,
  "est_vedette": 1
}
```

**Validations :**
- ID produit requis
- Produit doit exister
- Vérification que la catégorie existe si modifiée
- Authentification admin requise

#### DELETE - Supprimer un produit (Admin uniquement)
```json
{
  "id_produit": 1
}
```

**Protections :**
- Impossible de supprimer un produit avec des réservations actives
- Suppression automatique de l'image associée
- Authentification admin requise

### 4. API Statistiques (Nouvelle)

**Nouveau fichier :**
- `backend/api/api_statistiques.php`

**Endpoint :**
- `GET /backend/api/api_statistiques.php` (Admin uniquement)

**Données retournées :**
```json
{
  "success": true,
  "statistiques": {
    "total_produits": 25,
    "produits_disponibles": 20,
    "total_utilisateurs": 15,
    "total_reservations": 50,
    "reservations_en_attente": 5,
    "reservations_acceptees": 40,
    "total_categories": 8,
    "taux_disponibilite": 85.5,
    "produits_populaires": [...],
    "reservations_recentes": [...],
    "reservations_par_mois": [...],
    "reservations_par_statut": [...],
    "produits_par_categorie": [...]
  }
}
```

### 5. Frontend JavaScript

**Fichier modifié :**
- `assets/js/admin/gestion_produits.js`

**Corrections :**
- Utilisation de `success` au lieu de `succes`
- Utilisation de `produit`/`produits` au lieu de `donnees`
- Correction de la méthode DELETE (ajout du body JSON)

## Comment Tester

### 1. Se Connecter en tant qu'Administrateur

**Accès :**
- URL : `http://localhost/pages/connexion.html`
- Email : `admin@lendshare.fr`
- Mot de passe : `Admin123!`

### 2. Accéder à la Gestion des Produits

Après connexion, vous serez redirigé vers le tableau de bord admin.
Cliquez sur "Gestion des produits" dans le menu.

**URL directe :** `http://localhost/pages/admin/gestion_produits.php`

### 3. Tester l'Ajout d'un Produit

1. Cliquez sur le bouton "Ajouter un produit"
2. Remplissez le formulaire :
   - Nom du produit : "Perceuse électrique" (exemple)
   - Catégorie : Sélectionnez une catégorie
   - Quantité totale : 3
   - Quantité disponible : 3
   - Description : "Perceuse sans fil 18V avec batterie"
3. (Optionnel) Cliquez sur la zone d'image pour uploader une image
4. Cliquez sur "Enregistrer"

**Résultat attendu :**
- Message de succès "Produit créé avec succès"
- Le modal se ferme automatiquement
- Le tableau se recharge avec le nouveau produit

### 4. Tester la Modification d'un Produit

1. Dans le tableau, cliquez sur le bouton "✏️" (modifier) d'un produit
2. Le modal s'ouvre avec les données du produit
3. Modifiez les informations (par exemple, changez le nom ou la quantité)
4. Cliquez sur "Enregistrer"

**Résultat attendu :**
- Message de succès "Produit mis à jour avec succès"
- Le tableau se recharge avec les nouvelles informations

### 5. Tester la Suppression d'un Produit

1. Dans le tableau, cliquez sur le bouton "🗑️" (supprimer) d'un produit
2. Confirmez la suppression dans la popup

**Résultat attendu :**
- Le produit disparaît du tableau
- Si le produit a des réservations actives : message d'erreur

### 6. Tester les Filtres

**Recherche :**
- Tapez du texte dans la barre de recherche
- Le tableau filtre en temps réel

**Filtre par catégorie :**
- Sélectionnez une catégorie dans le menu déroulant
- Seuls les produits de cette catégorie s'affichent

**Filtre par disponibilité :**
- Sélectionnez "Disponible" ou "Indisponible"
- Le tableau filtre en conséquence

### 7. Tester le Toggle de Disponibilité

1. Dans le tableau, utilisez le switch de disponibilité d'un produit
2. Le statut change immédiatement

**Note :** Cette fonctionnalité utilise également l'endpoint PUT

## Sécurité

### Protection Admin
Toutes les opérations de modification (POST, PUT, DELETE) sont protégées :
- Vérification de session active
- Vérification du rôle administrateur
- Codes HTTP appropriés (401 pour non authentifié, 403 pour accès refusé)

### Validation des Données
- Validation côté serveur de toutes les données
- Protection contre les injections SQL (requêtes préparées)
- Vérification de l'existence des ressources avant modification/suppression

### Gestion des Erreurs
- Messages d'erreur explicites
- Codes HTTP appropriés
- Logs d'erreurs dans la console

## Structure des Fichiers

```
projet_mairie/
├── backend/
│   ├── api/
│   │   ├── api_produits.php (modifié - CRUD complet)
│   │   ├── api_statistiques.php (nouveau)
│   │   ├── api_connexion.php (modifié - sessions)
│   │   └── api_verifier_session.php (modifié - sessions)
│   ├── middleware/
│   │   └── middleware_authentification.php (nouveau)
│   └── config/
│       ├── database.php
│       └── config.php
├── pages/
│   └── admin/
│       ├── gestion_produits.php
│       ├── gestion_categories.php
│       ├── gestion_reservations.php
│       └── tableau_de_bord_admin.php
└── assets/
    └── js/
        └── admin/
            ├── gestion_produits.js (modifié)
            └── dashboard.js
```

## Prochaines Étapes Suggérées

1. **Intégrer l'API Statistiques au Dashboard**
   - Modifier `assets/js/admin/dashboard.js` pour utiliser `api_statistiques.php`
   - Afficher les graphiques et métriques

2. **Améliorer l'Upload d'Images**
   - Ajouter la sécurité admin à `api_upload_image.php`
   - Valider les types MIME côté serveur

3. **Ajouter des Logs Admin**
   - Créer une table `logs_admin` dans la base de données
   - Enregistrer toutes les actions admin (créations, modifications, suppressions)

4. **Améliorer la Gestion des Erreurs**
   - Créer des fichiers de logs structurés
   - Notifications en temps réel pour les admins

5. **Tests Automatisés**
   - Créer des tests unitaires pour les endpoints API
   - Tests d'intégration pour les flux complets

## Dépannage

### Problème : "Non authentifié" lors des opérations
**Solution :** Vérifiez que vous êtes bien connecté en tant qu'admin. Rafraîchissez la page de connexion.

### Problème : Les produits ne s'affichent pas
**Solution :** Vérifiez la console JavaScript (F12). Assurez-vous que l'API retourne bien `success: true` et `produits: [...]`

### Problème : Impossible de supprimer un produit
**Solution :** Vérifiez que le produit n'a pas de réservations actives. Clôturez ou refusez les réservations d'abord.

### Problème : L'image ne s'uploade pas
**Solution :**
- Vérifiez que le dossier `uploads/products/` existe et a les permissions d'écriture
- Vérifiez la taille du fichier (max 5 Mo)
- Vérifiez le format (JPEG, PNG, GIF, WEBP)

## Support

Pour toute question ou problème :
1. Consultez les logs du serveur PHP
2. Consultez la console JavaScript (F12)
3. Vérifiez les réponses des API dans l'onglet Network du navigateur

---

**Date de création :** 2025-11-23
**Version :** 1.0
**Développé avec Claude Code**
