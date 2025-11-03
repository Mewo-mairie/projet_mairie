# Déploiement sur Railway - LendShare Mairie

Guide complet pour déployer votre application sur Railway en 10 minutes.

---

## Prérequis

- Un compte GitHub (gratuit)
- Un compte Railway (gratuit)
- Votre projet sur GitHub

---

## Étape 1 : Préparer le projet pour Railway

### 1.1 Créer un fichier `Procfile`

Ce fichier dit à Railway comment lancer votre app.

Créez le fichier `Procfile` à la racine du projet :

```
web: php -S 0.0.0.0:$PORT
```

**C'est tout ce qu'il faut !**

### 1.2 Créer un fichier `runtime.txt`

Ce fichier spécifie la version de PHP.

Créez le fichier `runtime.txt` à la racine :

```
php-8.1
```

### 1.3 Créer un fichier `.railwayignore` (optionnel)

Pour exclure les fichiers inutiles :

```
projet_mairie_backup/
*.tmp
*.bak
.DS_Store
Thumbs.db
```

---

## Étape 2 : Pousser sur GitHub

### 2.1 Créer un repo GitHub

1. Aller sur https://github.com/new
2. Créer un repo `lendshare` (ou le nom que vous voulez)
3. **Ne pas initialiser** avec README (vous en avez déjà)

### 2.2 Pousser votre code

```bash
# Dans le dossier du projet
git init
git add .
git commit -m "Initial commit - LendShare Mairie"
git branch -M main
git remote add origin https://github.com/VOTRE_USERNAME/lendshare.git
git push -u origin main
```

**Remplacez `VOTRE_USERNAME` par votre username GitHub.**

---

## Étape 3 : Configurer Railway

### 3.1 Créer un compte Railway

1. Aller sur https://railway.app
2. Cliquer "Sign up"
3. Se connecter avec GitHub (recommandé)

### 3.2 Créer un nouveau projet

1. Cliquer sur "New Project"
2. Cliquer "Deploy from GitHub repo"
3. Autoriser Railway à accéder à GitHub
4. Sélectionner votre repo `lendshare`

### 3.3 Railway détecte automatiquement PHP

Railway va :
- Détecter `Procfile`
- Détecter `runtime.txt`
- Installer PHP 8.1
- Lancer votre app

Aucune configuration supplémentaire requise !

---

## Étape 4 : Configurer la base de données

### 4.1 Ajouter un service PostgreSQL (optionnel)

Si vous voulez une vraie base de données (au lieu de SQLite) :

1. Dans Railway, cliquer "Add Service"
2. Sélectionner "PostgreSQL"
3. Railway crée automatiquement la base

**Mais ce n'est pas nécessaire** - SQLite fonctionne aussi sur Railway.

### 4.2 Utiliser SQLite (recommandé pour commencer)

SQLite fonctionne parfaitement sur Railway. Aucune config supplémentaire.

---

## Étape 5 : Obtenir votre URL publique

Une fois déployé :

1. Dans Railway, aller à "Deployments"
2. Cliquer sur le déploiement actif
3. Copier l'URL publique (ex: `https://lendshare-production.up.railway.app`)

**C'est votre URL pour les jurés !**

---

## Résumé des fichiers à créer

### Fichier 1 : `Procfile`
```
web: php -S 0.0.0.0:$PORT
```

### Fichier 2 : `runtime.txt`
```
php-8.1
```

### Fichier 3 : `.railwayignore` (optionnel)
```
projet_mairie_backup/
*.tmp
*.bak
.DS_Store
Thumbs.db
```

---

## Checklist complète

- [ ] Créer `Procfile` à la racine
- [ ] Créer `runtime.txt` à la racine
- [ ] Créer `.railwayignore` (optionnel)
- [ ] Pousser sur GitHub (`git push`)
- [ ] Créer compte Railway
- [ ] Connecter GitHub à Railway
- [ ] Sélectionner le repo
- [ ] Attendre le déploiement (~2 minutes)
- [ ] Copier l'URL publique
- [ ] Tester l'application

---

## Tester votre déploiement

Une fois l'URL obtenue :

1. Ouvrir l'URL dans le navigateur
2. Vérifier que la page d'accueil s'affiche
3. Tester la connexion :
   - Email : `admin@lendshare.fr`
   - Password : `Admin123!`
4. Tester quelques fonctionnalités

**Si ça fonctionne, c'est bon !

---

## Mettre à jour l'application

Quand vous modifiez le code :

```bash
# Faire vos modifications
# ...

# Pousser sur GitHub
git add .
git commit -m "Description des changements"
git push origin main
```

Railway détecte automatiquement le push et redéploie en ~1 minute.

---

## Dépannage

### L'app ne démarre pas

**Vérifier les logs** :
1. Dans Railway, aller à "Logs"
2. Chercher les erreurs
3. Vérifier que `Procfile` existe et est correct

### Erreur "Port not available"

**Solution** : Railway gère automatiquement le port. Vérifier que `Procfile` utilise `$PORT` :
```
web: php -S 0.0.0.0:$PORT
```

### La base de données n'existe pas

**Solution** : Créer la base au premier déploiement :
1. SSH dans Railway (optionnel)
2. Ou modifier `index.php` pour créer la base automatiquement

### Erreur de permissions

**Solution** : Railway gère les permissions automatiquement. Pas d'action requise.

---

## Conseils

### Pour les jurés

Donnez-leur simplement l'URL :
```
https://lendshare-production.up.railway.app
```

Ils n'ont besoin de rien installer. Ils cliquent et c'est bon.

### Sécurité

**IMPORTANT** : Avant de donner l'URL aux jurés :

1. Changer les mots de passe par défaut
2. Ou créer des comptes spécifiques pour les jurés

```bash
# Créer un nouveau compte utilisateur
# Via l'interface d'admin
```

### Limites gratuites de Railway

- **5 projets** gratuits
- **500 heures** de compute par mois
- **5 GB** de stockage
- Suffisant pour une démo/test

---

## Exemple complet

### Votre structure finale

```
projet_mairie/
├── Procfile                    # ← Nouveau
├── runtime.txt                 # ← Nouveau
├── .railwayignore             # ← Nouveau (optionnel)
├── index.html
├── installer.php
├── README.md
├── INSTALLATION.md
├── assets/
├── backend/
├── pages/
├── database/
└── ... (tous vos fichiers)
```

### Commandes finales

```bash
# 1. Créer les fichiers (voir ci-dessus)

# 2. Pousser sur GitHub
git add Procfile runtime.txt .railwayignore
git commit -m "Add Railway deployment files"
git push origin main

# 3. Aller sur Railway et déployer
# (voir Étape 3)

# 4. Obtenir l'URL et la partager
```

---

## Ressources

- **Railway docs** : https://docs.railway.app
- **PHP sur Railway** : https://docs.railway.app/guides/php
- **GitHub** : https://github.com

---

## Résultat final

Après le déploiement, vous avez :

- Une URL publique (ex: `https://lendshare-production.up.railway.app`)
- Accessible 24/7
- Aucun serveur à gérer
- Mises à jour automatiques via GitHub
- Gratuit (avec limites)

Les jurés peuvent accéder directement sans rien installer !

---

## Besoin d'aide ?

Si ça ne fonctionne pas :

1. Vérifier les logs Railway
2. Vérifier que `Procfile` existe
3. Vérifier que le code est bien sur GitHub
4. Relancer le déploiement manuellement dans Railway

Bon déploiement !
