#  Guide d'installation - LendShare Mairie

Ce guide vous accompagne pas à pas dans l'installation de l'application LendShare.

---

##  Prérequis

### Logiciels requis

1. **PHP** (version 7.4 ou supérieure)
   - Télécharger : https://www.php.net/downloads
   - Vérifier l'installation : `php -v`

2. **Extensions PHP requises** :
   - `pdo` (généralement inclus)
   - `pdo_sqlite` (généralement inclus)
   - `json` (généralement inclus)
   - `mbstring` (peut nécessiter activation)

3. **Un navigateur web moderne** :
   - Chrome, Firefox, Edge, Safari (version récente)

### Vérifier les prérequis

Ouvrez un terminal et exécutez :

```bash
# Vérifier la version de PHP
php -v

# Vérifier les extensions PHP
php -m
```

Vous devriez voir `pdo`, `pdo_sqlite`, `json` et `mbstring` dans la liste des extensions.

---

## 🎯 Installation rapide (recommandé)

### Méthode 1 : Installation automatique avec le script

```bash
# 1. Ouvrir un terminal dans le dossier du projet
cd c:\Users\FrançoisBARON\Documents\Mewo\lendshare\projet_mairie

# 2. Exécuter le script d'installation
php installer.php

# 3. Suivre les instructions à l'écran
```

Le script va :
- Vérifier tous les prérequis système
- Créer la structure des dossiers
- Initialiser la base de données
- Créer les comptes utilisateur par défaut
- Configurer le fichier .gitignore

**Temps estimé : 2 minutes**

---

##  Installation manuelle (alternative)

Si vous préférez installer manuellement ou si le script automatique ne fonctionne pas :

### Étape 1 : Créer les dossiers

```bash
# Créer les dossiers nécessaires
mkdir database
mkdir cache
mkdir logs
mkdir uploads
mkdir uploads\products
mkdir uploads\categories
mkdir backups
mkdir backend\tests
```

### Étape 2 : Initialiser la base de données

```bash
# Exécuter le script de création de la base de données
php backend\config\create_database.php
```

### Étape 3 : Créer le fichier .gitignore

Créez un fichier `.gitignore` à la racine avec ce contenu :

```gitignore
# Base de données
database/*.db
database/*.db-journal

# Cache et logs
cache/*
logs/*
!cache/.gitkeep
!logs/.gitkeep

# Uploads
uploads/products/*
uploads/categories/*

# Backups
backups/*
projet_mairie_backup/

# Temporaires
*.tmp
*.bak
*.swp

# IDE
.vscode/
.idea/
```


## Démarrer l'application

### Avec le serveur PHP intégré (développement)

```bash
# Démarrer le serveur sur le port 8000
php -S localhost:8000

# Ou sur un autre port
php -S localhost:3000
```

Puis ouvrez votre navigateur : **http://localhost:8000**

### Avec XAMPP (Windows)

1. Installer XAMPP : https://www.apachefriends.org/
2. Copier le projet dans `C:\xampp\htdocs\lendshare\`
3. Démarrer Apache dans le panneau XAMPP
4. Ouvrir : **http://localhost/lendshare/**

### Avec WAMP (Windows)

1. Installer WAMP : https://www.wampserver.com/
2. Copier le projet dans `C:\wamp64\www\lendshare\`
3. Démarrer WAMP
4. Ouvrir : **http://localhost/lendshare/**

---

##  Comptes de test

Après l'installation, vous pouvez vous connecter avec :

### Compte Administrateur
- **Email** : `admin@lendshare.fr`
- **Mot de passe** : `Admin123!`
- **Accès** : Tableau de bord admin, gestion complète

### Compte Utilisateur
- **Email** : `test@test.fr`
- **Mot de passe** : `test123`
- **Accès** : Consultation et réservation de produits

### Changer le mot de passe admin

```bash
# Utiliser le script de réinitialisation
php backend\config\reset_admin_password.php
```

### Vérifier la structure de la base de données

```bash
# Afficher la structure des tables
php backend\config\check_table_structure.php
```

### Tester les mots de passe

```bash
# Vérifier que les mots de passe sont correctement hashés
php backend\tests\check_passwords.php
```

---

