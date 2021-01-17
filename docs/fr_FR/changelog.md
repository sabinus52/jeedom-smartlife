 A partir de septembre 2020, la plugin pour la version 3 de Jeedom ne bénéficiera plus des nouvelles mises à jour.
 Il sera necessaire de migrer vers la version 4 pour profiter des nouvelles fonctionnalités et des corrections.


# Version 1.1.3 du 17 janvier 2021

> <span style="color:blue">**Information**</span> : Cette version corrige l'erreur bloquante "*you cannot auth exceed once in 60 seconds*" pour la plupart des utilisateurs.

> <span style="color:red">**ATTENTION**</span> : Par contre, Tuya n'autorise la découverte des objets qu'**1 fois toutes les 5 minutes**. Du coup, la tâche planifiée de mise à jour des statuts **ne doit pas** descendre sous une fréquence de **6 min**.

- Fix l'erreur du Cloud Tuya : *you cannot auth exceed once in 60 seconds*


# Version 1.1.1 du 24 janvier 2020

- Fix compatibilité version PHP < 7.0
- Quelques petites corrections


# Version 1.1 du 21 décembre 2019

> **ATTENTION** : Depuis le 17 décembre 2019, le CLoud Tuya ne retourne plus le statut de la couleur pour les ampoules d'où l'erreur : `Param was not an HSL array`. C'est peut être un problème temporaire chez Tuya, j'ai donc désactivé la mise à jour de ce statut pour éviter les erreurs. L'action sur le changement de la couleur semble toujours fonctionner.

- Mise à jour de la documentation avec ajout d'un chapitre "Dépannage"
- Correction sur certaines versions de PHP
- Désactivation temporaire de la mise à jour des statuts de la couleur et la température pour les ampoules


# Version 1.0 du 10 novembre 2019

- Mise à jour de la documentation
- Création d'une branche pour jeedom V3


# Version 0.4 RC du 4 novembre 2019

> **ATTENTION** : Il obligatoire de relancer une "Découverte des objets" pour mettre à jour des paramètres pour le bon fonctionnement.

> **VOLET ROULANT** : Il faut supprimer l'objet et relancer une "Découverte des objets" pour avoir un code retour correct du statut de l'ouverture.

- Correction de bogue de l'API
- Quelques changements dans les paramètres des objets
- Corrections sur le code retour des volets roulants


# Version 0.3 beta du 26 octobre 2019

> **ATTENTION** : Il obligatoire de relancer une "Découverte des objets" pour mettre à jour des paramètres pour le bon fonctionnement.

- Correction de bogue de l'API
- Amélioration du mode "Découverte"
- Mise en place d'une tâche planifiée pour mise à jour des statuts des objets
- Essaie de plusieurs tentatives si le serveur Tuya ne répond pas
- Amélioration du code


# Version 0.2 beta du 7 octobre 2019

> **ATTENTION** : Refonte du système de création des objets. Après la mise à jour du plugin, et **avant de cliquer** sur "Découverte", il faut pour chaque objet, recliquer sur "Sauvegarder" pour mettre à jour certains éléments et éviter la création des objets en double lors de la "Découverte".

- Mise à jour de l'API
- Découverte automatique des objets
- Gestion des équipements inconnus
- Optimisation du nombre de requête au Cloud Tuya
- Nouvelle tentative en cas d'echec à la requête au Cloud Tuya
- Gestion des exceptions
- Plus de log en mode debug
- Quelques corrections


# Version 0.1 alpha du 10 août 2019

- Mise à jour de l'API
- Intégration du changement de couleur de la lampe
- Rafraichissement de l'état après une action
- Bug sur problème d'objet non reconnu


# Version 0.0 alpha du 30 mai 2019

- Développement initial
- Ajout des commandes d'informations
- Ajout des commandes On / Off
- Prise en compte des objets (switch, scene, cover, light)
