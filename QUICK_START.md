# Quick Start - LendShare Mairie

Guide ultra-rapide pour démarrer en 5 minutes.

---

## En 3 commandes

```bash
# 1. Installer
php installer.php

# 2. Démarrer
php -S localhost:8000

# 3. Ouvrir
# http://localhost:8000
```

---

## Se connecter

### Admin
```
Email: admin@lendshare.fr
Pass:  Admin123!
```

### User
```
Email: test@test.fr
Pass:  test123
```

---

## Nettoyer

```bash
php nettoyer.php
```

Supprime les fichiers doublons et le dossier backup.

---

## Documentation complète

- Installation détaillée : [INSTALLATION.md](INSTALLATION.md)
- Analyse du code : [ETAT_DES_LIEUX.md](ETAT_DES_LIEUX.md)
- README complet : [README.md](README.md)

---

## Problème ?

```bash
# Recréer la base de données
php backend/config/create_database.php

# Vérifier les mots de passe
php backend/tests/test_password_verify.php

# Changer de port si occupé
php -S localhost:8001
```

---

C'est tout ! Bonne utilisation.
