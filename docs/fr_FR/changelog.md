# Version 2.1.0 du 2 avril 2022

- Renommages des commandes *Allumer* et *Eteindre*
- Bouton pour recréer les commandes
- Mise à jour des patches de sécurité des dépendances


# Version 2.0.1 du 17 janvier 2022

> <span style="color:red">**ATTENTION**</span> : Depuis début 2022, le Cloud Tuya a rajouté **ENCORE** de nouvelles restrictions (lire la doc à ce sujet)

- Augmentation du delai
- Mise à jour de l'API pour la prise en compte des nouvelles restrictions


# Version 2.0.0 du 19 février 2021

> <span style="color:red">**ATTENTION**</span> : Depuis 2021, le Cloud Tuya a rajouté de nouvelles restrictions encore plus contraignantes (lire la doc à ce sujet)

- **Reécriture du code du plugin**
- Mise à jour de l'API pour la prise en compte des nouvelles restrictions
- Amélioration des logs
- Code retour des requêtes au Cloud
- Suppression des commandes 'infos' dont le Cloud ne remonte plus l'état


# Version 1.3.5 du 21 janvier 2021

- Fix le problème des climatiseurs qui ont une température avec un multiplicateur de 10


# Version 1.3.4 du 7 janvier 2021

> <span style="color:blue">**Information**</span> : Cette version corrige l'erreur bloquante "*you cannot auth exceed once in 60 seconds*" pour la plupart des utilisateurs.

> <span style="color:red">**ATTENTION**</span> : Par contre, Tuya n'autorise la découverte des objets qu'**1 fois toutes les 5 minutes**. Du coup, la tâche planifiée de mise à jour des statuts **ne doit pas** descendre sous une fréquence de **6 min**.

- Fix l'erreur du Cloud Tuya : *you cannot auth exceed once in 60 seconds*


# Version 1.3.3 du 3 décembre 2020

> <span style="color:red">**ATTENTION**</span> : Depuis le 25 novembre 2020, le CLoud Tuya n'autorise qu'une ouverture de session toutes les minutes. Pour éviter une attente de 60s entre les commandes, **il est recommandé dans vos scénarios de ne pas exécuter les commandes en parallèle**. Pour la mise à jour des statuts dans la tâche planifiée, **il est préférable de ne pas descendre la fréquence en dessous des 5 min**.

- Fix **temporairement** l'erreur du Cloud Tuya : *you cannot auth exceed once in 60 seconds*
- Fix la fonction de changement de température pour les objets de type *climatiseur*


# Version 1.3.2 du 19 novembre 2020

- Limite de la version de la dépendance de la librairie `PhpColors`


# Version 1.3.1 du 2 novembre 2020

- Désactivation de la vérification de la plateforme de `composer`


# Version 1.3 du 2 novembre 2020

- **Ajout de l'objet de type *climatiseur*** valable aussi pour les thermostats et vannes thermostatiques. *Seul le changement de température est intégré pour le moment.*
- Mise à jour des librairies
- Plugin au norme de la version 4 de Jeedom
- Fix si le Cloud Tuya retourne des données vides


# Version 1.2 du 27 juin 2020

- Ajout d'un paramètre timeout des requêtes au CLoud Tuya pour ceux qui ont un réseau lent ou mal configuré
- Ajout d'un paramètre "Activation automatique" des objets lors de la découverte
- Mise à jour de l'API
- Documentation : ajout d'un complément sur la compatibilité des équipements SmartLife


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
