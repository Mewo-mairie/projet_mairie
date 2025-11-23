# Documentation API - Lend&Share

## Table des matières
1. [Authentification](#authentification)
2. [Produits](#produits)
3. [Catégories](#catégories)
4. [Réservations](#réservations)
5. [Statistiques](#statistiques)
6. [Upload d'images](#upload-dimages)

---

## Authentification

### Connexion
**Endpoint:** `POST /backend/api/api_connexion.php`

**Corps de la requête:**
```json
{
  "email": "admin@lendshare.fr",
  "mot_de_passe": "Admin123!"
}
```

**Réponse (succès):**
```json
{
  "succes": true,
  "message": "Connexion réussie",
  "utilisateur": {
    "id": 1,
    "email": "admin@lendshare.fr",
    "role_utilisateur": "administrateur"
  }
}
```

**Session créée:**
- `$_SESSION['utilisateur_connecte']` = ID de l'utilisateur
- `$_SESSION['role_utilisateur']` = "administrateur" ou "utilisateur"

### Vérifier la session
**Endpoint:** `GET /backend/api/api_verifier_session.php`

**Réponse:**
```json
{
  "connecte": true,
  "utilisateur": {
    "id_utilisateur": 1,
    "nom_utilisateur": "Admin",
    "prenom_utilisateur": "Super",
    "email_utilisateur": "admin@lendshare.fr",
    "role_utilisateur": "administrateur"
  }
}
```

### Déconnexion
**Endpoint:** `GET /backend/api/api_deconnexion.php`

---

## Produits

### Lister tous les produits
**Endpoint:** `GET /backend/api/api_produits.php`

**Authentification:** Non requise

**Réponse:**
```json
{
  "success": true,
  "produits": [
    {
      "id_produit": 1,
      "nom_produit": "Tondeuse électrique",
      "description_produit": "Tondeuse sans fil",
      "id_categorie": 1,
      "nom_categorie": "Jardin",
      "image_url_produit": "uploads/products/tondeuse.jpg",
      "est_vedette": 1,
      "quantite_disponible": 2,
      "quantite_totale": 3,
      "date_ajout_produit": "2025-01-15 10:30:00"
    }
  ]
}
```

### Récupérer un produit spécifique
**Endpoint:** `GET /backend/api/api_produits.php?id=1`

**Authentification:** Non requise

**Réponse:**
```json
{
  "success": true,
  "produit": {
    "id_produit": 1,
    "nom_produit": "Tondeuse électrique",
    "description_produit": "Tondeuse sans fil",
    "id_categorie": 1,
    "nom_categorie": "Jardin",
    "image_url_produit": "uploads/products/tondeuse.jpg",
    "est_vedette": 1,
    "quantite_disponible": 2,
    "quantite_totale": 3,
    "date_ajout_produit": "2025-01-15 10:30:00"
  }
}
```

### Récupérer plusieurs produits par IDs
**Endpoint:** `GET /backend/api/api_produits.php?ids=1,2,3`

**Authentification:** Non requise

### Récupérer les produits en vedette
**Endpoint:** `GET /backend/api/api_produits.php?vedettes=1`

**Authentification:** Non requise

### Récupérer les produits par catégorie
**Endpoint:** `GET /backend/api/api_produits.php?categorie=1`

**Authentification:** Non requise

### Créer un produit
**Endpoint:** `POST /backend/api/api_produits.php`

**Authentification:** Administrateur requis

**Corps de la requête:**
```json
{
  "nom_produit": "Perceuse électrique",
  "description_produit": "Perceuse sans fil 18V",
  "id_categorie": 2,
  "image_url_produit": "uploads/products/perceuse.jpg",
  "est_vedette": 0,
  "quantite_totale": 5,
  "quantite_disponible": 5
}
```

**Champs requis:**
- `nom_produit` (string, non vide)
- `id_categorie` (integer, doit exister)

**Champs optionnels:**
- `description_produit` (string, défaut: "")
- `image_url_produit` (string, défaut: null)
- `est_vedette` (integer 0/1, défaut: 0)
- `quantite_totale` (integer, défaut: 1)
- `quantite_disponible` (integer, défaut: quantite_totale)

**Réponse (201 Created):**
```json
{
  "success": true,
  "message": "Produit créé avec succès",
  "produit": {
    "id_produit": 15,
    "nom_produit": "Perceuse électrique",
    "description_produit": "Perceuse sans fil 18V",
    "id_categorie": 2,
    "nom_categorie": "Bricolage",
    "image_url_produit": "uploads/products/perceuse.jpg",
    "est_vedette": 0,
    "quantite_disponible": 5,
    "quantite_totale": 5,
    "date_ajout_produit": "2025-11-23 14:30:00"
  }
}
```

**Erreurs possibles:**
- `400` - Nom du produit manquant
- `400` - Catégorie manquante
- `400` - Catégorie inexistante
- `401` - Non authentifié
- `403` - Permissions insuffisantes

### Modifier un produit
**Endpoint:** `PUT /backend/api/api_produits.php`

**Authentification:** Administrateur requis

**Corps de la requête:**
```json
{
  "id_produit": 15,
  "nom_produit": "Perceuse électrique Pro",
  "description_produit": "Perceuse sans fil 20V avec batteries",
  "id_categorie": 2,
  "quantite_totale": 8,
  "quantite_disponible": 6,
  "est_vedette": 1
}
```

**Champs requis:**
- `id_produit` (integer)

**Champs optionnels:** Tous les autres champs (les valeurs non fournies restent inchangées)

**Réponse (200 OK):**
```json
{
  "success": true,
  "message": "Produit mis à jour avec succès",
  "produit": {
    "id_produit": 15,
    "nom_produit": "Perceuse électrique Pro",
    ...
  }
}
```

**Erreurs possibles:**
- `400` - ID produit manquant
- `404` - Produit non trouvé
- `400` - Catégorie inexistante (si modifiée)
- `401` - Non authentifié
- `403` - Permissions insuffisantes

### Supprimer un produit
**Endpoint:** `DELETE /backend/api/api_produits.php`

**Authentification:** Administrateur requis

**Corps de la requête:**
```json
{
  "id_produit": 15
}
```

**Réponse (200 OK):**
```json
{
  "success": true,
  "message": "Produit supprimé avec succès"
}
```

**Protections:**
- Impossible de supprimer un produit avec des réservations actives (statut "en_attente" ou "accepte")
- L'image associée est automatiquement supprimée du serveur

**Erreurs possibles:**
- `400` - ID produit manquant
- `404` - Produit non trouvé
- `400` - Produit a des réservations actives
- `401` - Non authentifié
- `403` - Permissions insuffisantes

---

## Catégories

### Lister toutes les catégories
**Endpoint:** `GET /backend/api/api_categories.php`

**Réponse:**
```json
{
  "succes": true,
  "donnees": [
    {
      "id_categorie": 1,
      "nom_categorie": "Jardin",
      "description_categorie": "Outils de jardinage",
      "image_url_categorie": "uploads/categories/jardin.jpg",
      "date_creation": "2025-01-01 00:00:00"
    }
  ]
}
```

### Créer une catégorie
**Endpoint:** `POST /backend/api/api_categories.php`

**Authentification:** Administrateur requis (à vérifier)

### Modifier une catégorie
**Endpoint:** `PUT /backend/api/api_categories.php`

**Authentification:** Administrateur requis (à vérifier)

### Supprimer une catégorie
**Endpoint:** `DELETE /backend/api/api_categories.php`

**Authentification:** Administrateur requis (à vérifier)

---

## Réservations

### Lister toutes les réservations
**Endpoint:** `GET /backend/api/api_reservations.php`

**Authentification:** Requise

### Récupérer les réservations d'un utilisateur
**Endpoint:** `GET /backend/api/api_reservations.php?id_utilisateur=1`

**Authentification:** Requise (propre utilisateur ou admin)

### Créer une réservation
**Endpoint:** `POST /backend/api/api_reservations.php`

**Authentification:** Utilisateur requis

**Corps de la requête:**
```json
{
  "id_produit": 5
}
```

**Automatismes:**
- `id_utilisateur` récupéré depuis la session
- `statut_reservation` initialisé à "en_attente"
- `quantite_disponible` du produit décrémentée

### Modifier le statut d'une réservation
**Endpoint:** `PUT /backend/api/api_reservations.php`

**Authentification:** Administrateur requis

**Corps de la requête:**
```json
{
  "id_reservation": 10,
  "statut_reservation": "accepte"
}
```

**Statuts possibles:**
- `en_attente` - En attente de validation
- `accepte` - Acceptée par un admin
- `refuse` - Refusée par un admin
- `cloture` - Terminée (produit retourné)

**Automatismes:**
- Si refusé ou clôturé : `quantite_disponible` du produit incrémentée

---

## Statistiques

### Récupérer toutes les statistiques
**Endpoint:** `GET /backend/api/api_statistiques.php`

**Authentification:** Administrateur requis

**Réponse:**
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
    "produits_populaires": [
      {
        "id_produit": 5,
        "nom_produit": "Tondeuse électrique",
        "image_url_produit": "uploads/products/tondeuse.jpg",
        "nom_categorie": "Jardin",
        "nombre_reservations": 25
      }
    ],
    "reservations_recentes": [
      {
        "id_reservation": 50,
        "id_produit": 5,
        "id_utilisateur": 3,
        "nom_produit": "Tondeuse électrique",
        "image_url_produit": "uploads/products/tondeuse.jpg",
        "nom_utilisateur": "Dupont",
        "prenom_utilisateur": "Jean",
        "email_utilisateur": "jean@example.com",
        "statut_reservation": "en_attente",
        "date_reservation": "2025-11-23 10:00:00"
      }
    ],
    "reservations_par_mois": [
      {
        "mois": "2025-01",
        "nombre_reservations": 12
      }
    ],
    "reservations_par_statut": [
      {
        "statut_reservation": "en_attente",
        "nombre": 5
      },
      {
        "statut_reservation": "accepte",
        "nombre": 40
      }
    ],
    "produits_par_categorie": [
      {
        "id_categorie": 1,
        "nom_categorie": "Jardin",
        "nombre_produits": 8
      }
    ]
  }
}
```

**Erreurs possibles:**
- `401` - Non authentifié
- `403` - Permissions insuffisantes (non admin)

---

## Upload d'images

### Uploader une image
**Endpoint:** `POST /backend/api/api_upload_image.php`

**Authentification:** Administrateur requis

**Type de requête:** `multipart/form-data`

**Champs du formulaire:**
- `image` (file) - Le fichier image
- `type` (string) - "produit" ou "categorie"

**Contraintes:**
- Taille maximale : 5 Mo
- Formats acceptés : JPEG, PNG, GIF, WEBP
- Vérification MIME type côté serveur

**Réponse:**
```json
{
  "succes": true,
  "message": "Image uploadée avec succès",
  "donnees": {
    "url": "uploads/products/abc123_image.jpg",
    "nom_fichier": "abc123_image.jpg",
    "taille": 245678
  }
}
```

**Erreurs possibles:**
- `400` - Fichier manquant
- `400` - Type manquant ou invalide
- `400` - Fichier trop volumineux
- `400` - Format de fichier non autorisé
- `401` - Non authentifié
- `403` - Permissions insuffisantes

---

## Codes de Statut HTTP

### Succès
- `200 OK` - Requête réussie
- `201 Created` - Ressource créée avec succès

### Erreurs Client
- `400 Bad Request` - Données invalides ou manquantes
- `401 Unauthorized` - Non authentifié
- `403 Forbidden` - Permissions insuffisantes
- `404 Not Found` - Ressource non trouvée
- `405 Method Not Allowed` - Méthode HTTP non autorisée

### Erreurs Serveur
- `500 Internal Server Error` - Erreur serveur

---

## Middleware d'Authentification

Le fichier `backend/middleware/middleware_authentification.php` fournit trois fonctions :

### `verifierAuthentificationAdmin()`
Vérifie que l'utilisateur est connecté ET a le rôle "administrateur".
Retourne `401` si non authentifié, `403` si non admin, `true` sinon.

### `verifierAuthentificationUtilisateur()`
Vérifie que l'utilisateur est connecté (peu importe le rôle).
Retourne `401` si non authentifié, l'ID utilisateur sinon.

### `obtenirUtilisateurConnecte()`
Retourne l'ID de l'utilisateur connecté ou `null`.

---

## Exemples de Requêtes avec JavaScript

### Créer un produit
```javascript
const response = await fetch('/backend/api/api_produits.php', {
  method: 'POST',
  headers: {
    'Content-Type': 'application/json'
  },
  body: JSON.stringify({
    nom_produit: 'Perceuse électrique',
    description_produit: 'Perceuse sans fil 18V',
    id_categorie: 2,
    quantite_totale: 5,
    quantite_disponible: 5
  })
});

const data = await response.json();
if (data.success) {
  console.log('Produit créé:', data.produit);
}
```

### Modifier un produit
```javascript
const response = await fetch('/backend/api/api_produits.php', {
  method: 'PUT',
  headers: {
    'Content-Type': 'application/json'
  },
  body: JSON.stringify({
    id_produit: 15,
    nom_produit: 'Nouveau nom',
    quantite_disponible: 3
  })
});

const data = await response.json();
if (data.success) {
  console.log('Produit modifié:', data.produit);
}
```

### Supprimer un produit
```javascript
const response = await fetch('/backend/api/api_produits.php', {
  method: 'DELETE',
  headers: {
    'Content-Type': 'application/json'
  },
  body: JSON.stringify({
    id_produit: 15
  })
});

const data = await response.json();
if (data.success) {
  console.log('Produit supprimé');
}
```

---

## Notes Importantes

1. **Sessions PHP** : Toutes les APIs utilisent les sessions PHP. Assurez-vous que `session_start()` est appelé.

2. **CORS** : Les headers CORS sont configurés pour `Access-Control-Allow-Origin: *` (développement). Restreignez cela en production.

3. **Sécurité** : Toutes les requêtes SQL utilisent des requêtes préparées (PDO) pour prévenir les injections SQL.

4. **Validation** : La validation est effectuée côté serveur. Ne vous fiez jamais uniquement à la validation côté client.

5. **Format de réponse** : Certaines APIs utilisent `success` tandis que d'autres utilisent `succes`. À normaliser dans une future version.

---

**Date de création :** 2025-11-23
**Version :** 1.0
