# TomTroc

Application PHP simple de troc de livres avec gestion des utilisateurs, des livres et de la messagerie.

## Prérequis

- PHP 8.x (compatible avec PHP 8.2)
- MySQL / MariaDB
- XAMPP ou un serveur Apache/PHP local
- Navigateur Web

## Installation

1. Copier le projet dans le dossier de votre serveur local.
   - Exemple avec XAMPP : `C:\xampp\htdocs\TomTroc`

2. Démarrer Apache et MySQL depuis le panneau de contrôle XAMPP.

3. Importer la base de données.
   - Ouvrir phpMyAdmin ou un autre outil MySQL.
   - Créer une nouvelle base nommée `tomtroc`.
   - Importer le fichier SQL situé dans `SQL/tomtroc.sql`.

4. Vérifier la configuration de la base de données.
   - Le projet utilise la classe `Models/DBManager.php`.
   - Paramètres par défaut :
     - hôte : `localhost`
     - base : `tomtroc`
     - utilisateur : `root`
     - mot de passe : `` (vide)

5. Placer les fichiers et dossiers en place.
   - Le point d’entrée principal est `index.php`.
   - Les contrôleurs sont dans `Controllers/`.
   - Les modèles sont dans `Models/`.
   - Les vues sont dans `Views/`.

## Lancement

- Ouvrir votre navigateur et aller sur : `http://localhost/TomTroc/`
- Si le projet est dans un sous-dossier différent, ajuster l’URL en conséquence.

## Routes principales

- `index.php?action=accueil` : page d’accueil
- `index.php?action=livreEx&search=...` : recherche de livres
- `index.php?action=showBook&id=...` : détail d’un livre
- `index.php?action=login` : page de connexion
- `index.php?action=signup` : page d’inscription
- `index.php?action=monCompte` : profil utilisateur
- `index.php?action=messagerie&user_id=...` : messagerie

## Fonctionnalités

- Inscription et connexion d’utilisateur
- Gestion de compte
- Ajout, modification et suppression de livres
- Recherche de livres
- Messagerie entre utilisateurs

## Conseils

- Si vous devez modifier les identifiants de connexion MySQL, mettez à jour `Models/DBManager.php`.
- Assurez-vous que `assets/` contienne bien les images et fichiers nécessaires.

## Support

- En cas d’erreur de base de données, vérifier que la base `tomtroc` existe et que les tables ont bien été importées.
- Vérifier également les droits d’accès aux fichiers dans le dossier du projet.
