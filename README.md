# SIAM 2024 - Cahier des charges
En qualité d’assistant ingénieur développement, vous êtes chargé de concevoir et réaliser via un site web dynamique qui permet de créer, rejoindre et jouer des parties du jeu de société Siam.

## Fonctionnement de l'application
### 1. Authentification
Vous devrez gérer une table des utilisateurs qui contient au départ au moins un administrateur. Les mots de passe ne sont pas stockés en clair.

**Un utilisateur (administrateur ou joueur) doit pouvoir** : 

- s'authentifier
- modifier son mot de passe
- créer et rejoindre une partie en attente d'un joueur
- visualiser la liste des parties à rejoindre
- visualiser la liste des parties que cet utilisateur a en cours. pour chaque partie, mettre  évidence le joueur dont c'est le tour
- jouer un coup dans une partie en cours
- se déconnecter


**Un administrateur doit pouvoir** :

- créer un compte joueur
- jouer dans n'importe quelle partie quelque soit le joueur actif
- supprimer une partie en cours

## Jeu Siam

vous devrez gérer une table des parties. Modéliser le plateau de jeu pour stocker les informations de pièces et de direction.

### La page du jeu:

- affiche le plateau dans sa configuration actuelle
- indique le joueur dont c'est le tour
- affiche le nombre de pièces en réserve pour chaque joueur
- met en évidence la dernière pièce à avoir été déplacée
- si c'est au joueur actif de jouer, lui propose de sélectionner une pièce du plateau ou de la réserve (toutes mises en évidence), puis recharge la page.
- à l'étape suivante, lui propose de cliquer sur une des zones de destination possibles et mise en évidence, ou d'annuler la sélection initiale, puis recharge la page.


**Il faut bien sûr tester les conditions de victoire et détecter la fin de partie!**

### pour aller plus loin: ajax
Plutôt que recharger la page lorsqu'on sélectionne une pièce à déplacer, on pourra demander au serveur la liste des cases possibles en ajax, de manière à proposer les cases de destination possibles sans rechargement (donc modification des zones de sélection en javascript).

## Contraintes techniques

__L'application 3-tiers devra utiliser les technologies suivantes__  :

- Côté client JavaScript et jquery + HTML5 + CSS (bootstrap autorisé).
- La partie serveur se fera en PHP 7+ (sans framework)
- La base de données sera SQLite3 

Toute utilisation d'une bibliothèque/API/framework annexe devra être validée au préalable par l'enseignant (ou si vous voulez gagner du temps: la réponse est non)

D'un point de vue sécurité, __toute injection de code trop aisée sera sévèrement sanctionnée__.

Les projets doivent pouvoir fonctionner sur les machines des salles TP (en php7).
Annexe 1. règles du jeu et ressources graphiques Cf zip sur Moodle
Annexe 2. Fiches de test Cf document instructions_fiche_de_test.pdf sur Moodle
Annexe 3. **Infos pratiques**
- Projet à réaliser en binôme, sauf autorisation explicite
choisir un groupe SAE pour la composition du binôme sur Moodle avant le 12 février 2024
- Date de rendu du modèle de données : **26 février 2024**
- Date de rendu du projet : **18 mars 2024**

1. Sur gitlab.univ-artois.fr
Un des étudiants crée le projet, invite ses camarades, et le responsable de l’unité
- code source (fichiers html, js, img, php, etc, organisés de façon efficiente)
- base de données sqlite

2. Sur Moodle
- Rapport technique pour le groupe (ne pas oublier les identifiants adm!)
- Fiches de tests de l’application
- Portfolio individuel (partie prise en charge, difficultés rencontrées, communication avec le binôme, si c'était à refaire?)

Changelog
- v1.0 – version initiale
