![capture](../images/icon-48.png) **Documentation du plugin SmartLife**


# Description 

Ce plugin permet de contrôler les objects connectés SmartLife ou Tuya.

**Objets UNIQUEMENT compatibles** :
- Prises connectées
- Scènes
- Interrupteurs pour volets roulants
- Ampoules connectées

Voir le chapitre [Objets compatibles](#Objets%20compatibles) pour savoir si votre objet sera compatible.


>  A partir de septembre 2020, la plugin pour la version 3 de Jeedom ne bénéficiera plus des nouvelles mises à jour. Il sera necessaire de migrer vers la version 4 pour profiter des nouvelles fonctionnalités et des corrections.


# Configuration du plugin

Après téléchargement du plugin, il vous suffit juste d’activer celui-ci et de saisir son compte de connexion qui a été créé depuis l'application mobile :

- Son identifiant utilisateur soit son email ou son numéro de téléphone
- Son mot de passe
- Code du pays ou indicatif du téléphone
- Nom de l'application qui a été utilisée lors de l'inscription

**Sauvegarder les informations précédemment saisies** et après il est possible de faire un test pour vérifier la bonne connexion avec les serveurs Tuya.

- le paramètre "Activation automatique" permet d'activer ou de désactiver, ainsi que la visibilité de l'objet automatiquement lors d'une découverte si ceux-ci sont en ligne ou pas. Si la valeur est à Non alors, à la 1ère découverte, aucun objet ne sera activé.
- le paramètre "Timeout" défini par défaut à 5 secondes, pour les requêtes à l'API Tuya.



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

- ***Le Cloud Tuya interdit un trop grand nombre de connexion, il est <span style="color:red">**IMPERATIF et OBLIGATOIRE**</span> de ne pas descendre la fréquence en dessous des <span style="color:orange">6 min</span>.***
- L'état de l'objet est rafraichit après une action effectuée dans Jeedom.
- Si une action est réalisée depuis l'application SmartLife ou Tuya sur son smartphone, alors l'état de l'objet **ne sera pas mis à jour** dans ce cas.



# Objets compatibles


### Objets UNIQUEMENT compatibles :
- Prises connectées
- Scènes
- Interrupteurs pour volets roulants
- Ampoules connectées


### Objets NON compatibles :
- Détecteur de porte
- Détecteur de mouvement
- Sirène
- Caméra

Ces objets de type *"à risque"* ne sont pas recommandés à être pilotés par le Cloud, donc ils  ne seront **JAMAIS** intégrés dans le plugin.


### Notes importantes

A partir de différents retours de chacun, certains objets de type `lampe` ont un comportement différent en fonction du fabriquant. Avec ce constat, il est particulièrement difficile d'adapter et d'apporter des corrections pour ces équipements.

> <span style="color:red">**ATTENTION**</span> : Depuis le 17 décembre 2019, le CLoud Tuya ne retourne plus le statut de la couleur pour les ampoules d'où l'erreur : `Param was not an HSL array`. C'est peut être un problème temporaire chez Tuya, j'ai donc désactivé la mise à jour de ce statut pour éviter les erreurs. L'action sur le changement de la couleur semble toujours fonctionner.



# Dépannage

Voici une liste non exhautive de différents problèmes que l'on peut rencontrer

### 1. Problème de connexion ou bouton `Tester la connexion` ne fonctionne pas

Pour analyser ce problème, il faut lancer la commande `curl` depuis la box jeedom

Avec une utilisation de l'application Smartlife
~~~ bash
curl -v -X POST -k -H 'Content-Type: application/x-www-form-urlencoded' -i 'https://px1.tuyaus.com/homeassistant/auth.do' --data 'userName=LOGIN&password=PASSORD&countryCode=33&bizType=smart_life&from=tuya'
~~~

Avec une utilisation de l'application Tuya
~~~ bash
curl -v -X POST -k -H 'Content-Type: application/x-www-form-urlencoded' -i 'https://px1.tuyaus.com/homeassistant/auth.do' --data 'userName=LOGIN&password=PASSORD&countryCode=33&bizType=smart_life&from=tuya'
~~~

Il faut remplacer LOGIN et PASSWORD par ton nom d'utilisateur et ton mot de passe

Si la commmande fonctionne, un retour correct doit être de la forme suivante :
> {"access_token":"EUheu15446X6245Y31WxJAC5HRxarNDgj","refresh_token":"EUheu15446062XY731WxJAhCRkP8zBQ7b","token_type":"bearer","expires_in":864000}


### 2. Erreur : `cURL error 28: Resolving timed out after XXXX milliseconds ...`

C'est un problème de configuration réseau de la box Jeedom. Merci de vérifier les serveurs DNS.



# Liens utiles

- *Topic sur forum Jeedom Community* : https://community.jeedom.com/t/plugin-smartlife-tuya-discussion-generale
- *Notes de version* : https://sabinus52.github.io/jeedom-smartlife/fr_FR/changelog
- *Dépôt Github* : https://github.com/sabinus52/jeedom-smartlife
- *Soumettre un bogue* : https://github.com/sabinus52/jeedom-smartlife/issues
