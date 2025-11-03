# Railway - Démarrage rapide

Déployer en 5 minutes.

---

## Les 5 étapes

### 1. Créer un repo GitHub

```bash
git init
git add .
git commit -m "Initial commit"
git branch -M main
git remote add origin https://github.com/VOTRE_USERNAME/lendshare.git
git push -u origin main
```

Remplacez `VOTRE_USERNAME` par votre username GitHub.

---

### 2. Créer un compte Railway

Aller sur https://railway.app et se connecter avec GitHub.

---

### 3. Créer un projet Railway

1. Cliquer "New Project"
2. Cliquer "Deploy from GitHub repo"
3. Sélectionner votre repo `lendshare`

Railway détecte automatiquement PHP et déploie !

---

### 4. Attendre le déploiement

Environ 2 minutes. Vous verrez les logs défiler.

---

### 5. Copier l'URL publique

Une fois déployé, vous avez une URL comme :
```
https://lendshare-production.up.railway.app
```

Donnez cette URL aux jurés !

---

## Fichiers créés automatiquement

Ces fichiers sont déjà dans le projet :
- Procfile - Comment lancer l'app
- runtime.txt - Version PHP
- .railwayignore - Fichiers à ignorer

Rien à faire, c'est prêt !

---

## Mettre à jour

```bash
git add .
git commit -m "Vos changements"
git push origin main
```

Railway redéploie automatiquement en environ 1 minute.

---

## Aide complète

Voir : [RAILWAY_DEPLOYMENT.md](RAILWAY_DEPLOYMENT.md)

---

C'est tout !
