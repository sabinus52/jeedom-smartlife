![capture](../images/icon-48.png) **Documentation du plugin SmartLife**


# Description 

Ce plugin permet de contrôler les objects connectés SmartLife ou Tuya.

**Objets UNIQUEMENT compatibles** :
- Prises connectées
- Scènes
- Interrupteurs pour volets roulants
- Ampoules connectées
- Climatiseur

Voir le chapitre [Objets compatibles](#Objets%20compatibles) pour savoir si votre objet sera compatible.

<span style="color:red">**INFO** : Vu la médiocrité des objets Tuya en Wifi, je ne vois plus l'intérêt à faire des évolutions pour ce plugin. Ces objets ne sont vraiment pas destinés à faire de la domotique. Je maintiendrai les correctifs et la mise à jour des patchs de sécurité.</span>

<span style="color:orange">**Merci de lire les [notes importantes](#Notes%20importantes).**</span>


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

**Depuis 2021, le Cloud Tuya a rajouté de nouvelles restrictions encore plus contraignantes, puis aussi début 2022 :**
- Découverte des objets : ***1 fois toutes les 15 minutes***
- Recupération des états d'un objet : ***1 fois toutes les 2 minutes***
- Si une personne possède 20 objets, il faudra dans ce cas 20 minutes pour récupérer tous les états de ses objets.
- Si une action est réalisée depuis l'application SmartLife ou Tuya sur son smartphone, alors l'état de l'objet **ne sera pas mis à jour** dans ce cas.

<span style="color:red">Je conseille donc de ne pas descendre la fréquence en dessous de **17 min**.</span>


# Objets compatibles


### Objets UNIQUEMENT compatibles :
- Prises connectées
- Scènes
- Interrupteurs pour volets roulants
- Ampoules connectées
- Climatiseurs et certaines vannes thermostatiques


### Objets NON compatibles :
- Détecteur de porte
- Détecteur de mouvement
- Sirène
- Caméra

Ces objets ne seront donc **JAMAIS** gérés par le plugin.


### Objets pouvant être intégrés

Pour savoir si un objet peut être **ÉVENTUELLEMENT** compatible, il faut passer les logs en mode *debug*, et vérifier qu'on a une ligne de type suivante :
~~~
[2099-01-01 00:00:00][DEBUG] : DISCOVERY : Objet non pris en compte Sabinus\TuyaCloudApi\Device\UnknownDevice Object ( [devType:protected] => climate [id:protected] => 0000000000000000000000 [type:protected] => unknown [name:protected] => Nom objet [icon:protected] => https://images.tuyaeu.com/smart/icon/123456789.png [data:protected] => Array ( [online] => [state] => false ) )
~~~
Si c'est le cas, merci d'ouvrir une [`issue sur Github`](https://github.com/sabinus52/jeedom-smartlife/issues) et je verrai si c'est possible ou pas d'intégrer ce type d'objet dans le plugin.


### Notes importantes

A partir de différents retours de chacun, certains objets de type `lampe` et `climatiseur` ont un comportement différent en fonction du fabriquant. Avec ce constat, il est particulièrement difficile d'adapter et d'apporter des corrections pour ces équipements.

> <span style="color:red">**ATTENTION**</span> : Depuis le 17 décembre 2019, le CLoud Tuya ne retourne plus le statut de la couleur pour les ampoules d'où l'erreur : `Param was not an HSL array`. C'est peut être un problème temporaire chez Tuya, j'ai donc désactivé la mise à jour de ce statut pour éviter les erreurs. L'action sur le changement de la couleur semble toujours fonctionner.

> <span style="color:red">**ATTENTION**</span> : Depuis le 25 novembre 2020, le CLoud Tuya n'autorise qu'une ouverture de session toutes les minutes. Pour éviter une attente de 60s entre les commandes, **il est recommandé dans vos scénarios de ne pas exécuter les commandes en parallèle**.

> <span style="color:red">**ATTENTION**</span> : Depuis 2021, le Cloud Tuya a rajouté de nouvelles restrictions encore plus contraignantes (voir information plus haut)

> <span style="color:red">**ATTENTION**</span> : Depuis le début de l'année 2022, le Cloud Tuya a encore rajouté de nouvelles restrictions

> <span style="color:red">**NOTE** d'avril 2022</span> : Vu la médiocrité des objets Tuya en Wifi, je ne vois plus l'intérêt à faire des évolutions pour ce plugin. Ces objets ne sont vraiment pas destinés à faire de la domotique.

Avec ces nouvelles restrictions, il est de plus en plus dur de faire des rafraichissements des états des objets.

Si cela devait continuer dans ce sens, je ne verrai plus l'intérêt de maintenir ce plugin. Je conseille de fuir les objets 
Wifi Tuya qui ne sont pas adaptés à la vraie domotique.

Pour ceux qui veulent absolument le retour des états, je vous conseille d'utiliser le plugin [wifilightV2](https://market.jeedom.com/index.php?v=d&p=market_display&id=2793)


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
