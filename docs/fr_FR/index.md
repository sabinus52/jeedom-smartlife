![capture](../images/icon-48.png) **Documentation du plugin SmartLife**

**Plugin en cours de développement, il est encore pour le moment en version NON STABLE**


# Description 

Ce plugin permet de contrôler les objects connectés SmartLife ou Tuya.


**Objets compatibles pour le moment** :
- Prises connectées
- Scènes
- Interrupteurs pour volets roulants
- Ampoules connectées

Les autres objets ne sont pas encore pris en compte. Certains objets comme le détecteur de porte, la sirène ne sont pas reconnus par l'API et ne seront donc pas gérés par le plugin.


# Configuration du plugin

Après téléchargement du plugin, il vous suffit juste d’activer celui-ci et de saisir son compte de connexion qui a été créé depuis l'application mobile :

- Son identifiant utilisateur soit son email ou son numéro de téléphone
- Son mot de passe
- Code du pays ou indicatif du téléphone
- Nom de l'application qui a été utilisée lors de l'inscription

Sauvegarder les informations et après il est possible de faire un test pour vérifier la bonne connexion avec les serveurs Tuya.


# Configuration des équipements

> **ATTENTION à partir de la version 0.2** : Refonte du système de création des objets. Après la mise à jour du plugin, et **avant de cliquer** sur "Découverte", il faut pour chaque objet, recliquer sur "Sauvegarder" pour mettre à jour certains éléments et éviter la création des objets en double lors de la "Découverte".

Il n'y a pas besoin d'ajouter un objet, aller sur *Plugins / Objets connectés / Objets SmartLife/Tuya*

Puis cliquer simplement sur l'icône **Découverte des objets** pour ajouter automatiquement tous vos objets reconnus par le plugin.

Il ne reste plus qu'à aller sur chaque objet pour changer son nom et redéfinir d'autres paramètres au besoin.

Les objets détectés en mode *Online* par les serveurs Tuya sont en mode *activer* et *visible* dans Jeedom.


# Rafraîchissement des états des objets

Une tâche planifiée est disponible mais **n'est pas activée** par défaut.

Pour l'activer et choisir la fréquence de mise à jour, aller dans le *Moteur de tâches* depuis le menu d'administration.

![Tache planifiée](../images/cron.png)


**Notes importantes à ce sujet :**

- L'état de l'objet est rafraichit après une action effectuée dans Jeedom.
- Si une action est réalisée depuis l'application SmartLife ou Tuya sur son smartphone, alors l'état de l'objet **ne sera pas mis à jour** dans ce cas. Pour contourner, il est possible d'intéragir avec le plugin IFTTT.