# LendShare - Système de prêt d'objets pour mairie

Application web de gestion de prêt d'objets municipaux développée pour faciliter le partage de matériel entre citoyens.

---

## Description

LendShare est une plateforme web permettant aux citoyens d'emprunter du matériel municipal (outils, équipements sportifs, matériel de jardinage, etc.) de manière simple et organisée.

### Fonctionnalités principales

#### Pour les utilisateurs
- Consultation du catalogue de produits disponibles
- Réservation de produits en ligne
- Gestion de compte personnel
- Historique des réservations
- Suivi des emprunts en cours

#### Pour les administrateurs
- Gestion complète des produits (ajout, modification, suppression)
- Gestion des catégories
- Gestion des utilisateurs
- Tableau de bord avec statistiques
- Validation des réservations

---

## Installation rapide

### Installation automatique (recommandé)

```bash
# 1. Cloner ou télécharger le projet
cd projet_mairie

# 2. Exécuter le script d'installation
php installer.php

# 3. Démarrer le serveur
php -S localhost:8000

# 4. Ouvrir dans le navigateur
# http://localhost:8000
```

Pour plus de détails, consultez [INSTALLATION.md](INSTALLATION.md)

---

## Prérequis

- PHP 7.4 ou supérieur
- Extensions PHP : pdo, pdo_sqlite, json, mbstring
- Navigateur web moderne (Chrome, Firefox, Edge, Safari)

Aucune base de données externe requise - SQLite est utilisé (fichier local).

---

## Comptes de test

### Administrateur
```
Email    : admin@lendshare.fr
Password : Admin123!
```

### Utilisateur
```
Email    : test@test.fr
Password : test123
```

IMPORTANT : Changez ces mots de passe en production.

---

## Structure du projet

```
projet_mairie/
├── assets/              # Ressources frontend (CSS, JS, images)
├── backend/             # Code backend PHP
│   ├── api/            # Points d'API REST
│   ├── config/         # Configuration et base de données
│   └── models/         # Modèles de données
├── pages/              # Pages HTML
│   └── admin/          # Interface d'administration
├── database/           # Base de données SQLite
├── cache/              # Cache applicatif
├── logs/               # Fichiers de logs
└── uploads/            # Images uploadées

Fichiers clés :
├── installer.php       # Script d'installation automatique
├── nettoyer.php        # Script de nettoyage du projet
├── INSTALLATION.md     # Guide d'installation détaillé
├── ETAT_DES_LIEUX.md  # Analyse du code
└── README.md           # Ce fichier
```

---

## Technologies utilisées

### Backend
- PHP 7.4+ (vanilla, sans framework)
- SQLite (base de données fichier)
- PDO (accès base de données)

### Frontend
- HTML5 / CSS3
- JavaScript (vanilla, sans framework)
- Google Fonts (Roboto)

### Pas de dépendances externes
- Pas de Composer
- Pas de npm/yarn
- Pas de framework lourd
- Code simple et accessible

---

## API Endpoints

### Authentification
```
POST   /backend/api/api_connexion.php        # Connexion
POST   /backend/api/api_deconnexion.php      # Déconnexion
POST   /backend/api/api_inscription.php      # Inscription
GET    /backend/api/api_verifier_session.php # Vérifier session
```

### Produits
```
GET    /backend/api/api_produits.php          # Liste des produits
POST   /backend/api/api_produits.php          # Créer un produit
PUT    /backend/api/api_produits.php          # Modifier un produit
DELETE /backend/api/api_produits.php          # Supprimer un produit
```

### Catégories
```
GET    /backend/api/api_categories.php        # Liste des catégories
POST   /backend/api/api_categories.php        # Créer une catégorie
```

### Réservations
```
GET    /backend/api/api_reservations.php      # Liste des réservations
POST   /backend/api/api_reservations.php      # Créer une réservation
PUT    /backend/api/api_reservations.php      # Modifier une réservation
```

---

## Personnalisation

### Modifier les couleurs

Éditez `assets/common.css` :

```css
/* Couleur principale */
--couleur-primaire: #0066ff;

/* Couleur secondaire */
--couleur-secondaire: #667eea;

/* Couleur de fond */
--couleur-fond: #f7f4f7;
```

### Ajouter un logo

Remplacez `assets/logo.png` par votre logo (format PNG, dimensions recommandées : 200x60px).

### Modifier les textes

Les textes sont dans les fichiers HTML :
- Page d'accueil : `index.html`
- Page connexion : `pages/connexion.html`
- Pages admin : `pages/admin/`

---

## Nettoyage du projet

Le projet contient des fichiers doublons et de test. Pour nettoyer :

```bash
php nettoyer.php
```

Ce script va :
- Supprimer les fichiers CSS doublons (versions anglaises)
- Supprimer le dossier `projet_mairie_backup`
- Réorganiser les scripts de test dans `backend/tests/`
- Nettoyer les fichiers temporaires

Faites une sauvegarde avant d'exécuter ce script.

---

## Dépannage

### Le serveur ne démarre pas
```bash
# Vérifier la version PHP
php -v

# Vérifier que le port n'est pas occupé
php -S localhost:8001  # Essayer un autre port
```

### Erreur de base de données
```bash
# Recréer la base de données
php backend/config/create_database.php
```

### Impossible de se connecter
```bash
# Vérifier les mots de passe
php backend/tests/test_password_verify.php

# Réinitialiser le mot de passe admin
php backend/config/reset_admin_password.php
```

Pour plus d'aide, consultez [INSTALLATION.md](INSTALLATION.md) section Dépannage

---

## Documentation

- [INSTALLATION.md](INSTALLATION.md) - Guide d'installation complet
- [ETAT_DES_LIEUX.md](ETAT_DES_LIEUX.md) - Analyse détaillée du code
- [accounts.MD](accounts.MD) - Comptes de test
- Cahier_des_charges_LendShare_Mairie 1.pdf - Spécifications complètes

---

## Sécurité

### Recommandations pour la production

1. **Changer les mots de passe par défaut**
   ```bash
   php backend/config/reset_admin_password.php
   ```

2. **Configurer HTTPS** (certificat SSL/TLS)

3. **Désactiver l'affichage des erreurs**
   - Dans `backend/config/config.php` : `ini_set('display_errors', 0);`

4. **Protéger les dossiers sensibles**
   - Ajouter `.htaccess` dans `database/`, `logs/`, `cache/`

5. **Sauvegardes régulières**
   - Base de données : `database/lendshare.db`
   - Images : `uploads/`

---

## Code quality

### Points forts
- Code en français (lisible pour développeurs francophones)
- Nommage explicite et descriptif
- Commentaires détaillés (JSDoc, PHPDoc)
- Structure modulaire claire
- Séparation des préoccupations
- Pas de dépendances externes complexes

### Orienté junior-friendly
- Variables explicites : `$email_utilisateur`, `$mot_de_passe_utilisateur`
- Fonctions descriptives : `gererSoumissionFormulaireConnexion()`
- Code linéaire et facile à suivre
- Commentaires pour chaque section importante

**Pour l'analyse complète, voir [ETAT_DES_LIEUX.md](ETAT_DES_LIEUX.md)**

---

## Contribution

Ce projet est conçu pour être facilement modifiable.

### Pour contribuer :
1. Lire le code et la documentation
2. Suivre les conventions de nommage en français
3. Commenter le code de manière explicite
4. Tester avant de commit

### Conventions de code :
- **PHP** : PSR-12 adapté (noms en français)
- **JavaScript** : Commentaires JSDoc
- **CSS** : BEM-like avec noms français
- **Commits** : Messages en français, clairs et descriptifs

---

## Licence

Ce projet est développé pour un usage municipal et éducatif.

---

## Support

Pour toute question ou problème :

1. **Consultez la documentation** : [INSTALLATION.md](INSTALLATION.md)
2. **Vérifiez l'état des lieux** : [ETAT_DES_LIEUX.md](ETAT_DES_LIEUX.md)
3. **Vérifiez les logs** : `logs/app.log`
4. **Console navigateur** : F12 → Console

---

## Roadmap

### Version actuelle : 1.0.0
- Système de connexion/inscription
- Gestion des produits
- Gestion des catégories
- Système de réservation
- Interface d'administration
- Gestion des utilisateurs

### Améliorations futures possibles
- Notifications par email
- Version mobile responsive optimisée
- Statistiques avancées
- Recherche avancée avec filtres
- Calendrier de disponibilité
- Multi-langues
- Export de données (PDF, Excel)

---

## Remerciements

Développé pour faciliter le partage de ressources municipales et encourager l'économie collaborative au niveau local.
