# RELEASE NOTE Version 2.X

A cause des restrictions du Cloud Tuya pour limiter le nombre de requêtes, le code a du être adapté et ce dernier conserve en cache les requêtes trop fréquentes.


## Class `Session`

- Aucun changement


## Class `TuyaCloudApi`

- Suppression des fonctions *controlDevice()* et *sendEvent()*

- Ajout de la fonction de test de connexion
~~~ php
$isSuccess = $api->checkConnection();
~~~

- Ajout de la fonction *getSession()*

- La fonction *discoverDevices()* ne retourne plus la liste des objets trouvés, mais un code retour pour savoir si la requête a réussi ou provient du cache.

- En échange la fonction *getAllDevices()* retourne la liste des objets trouvés.


## Abstract Class `Request`

Nouvelle classe abstraite pour chaque type de requêtes au CLoud Tuya


### Class `DiscoveryRequest`

~~~ php
$session = new Session('login', 'password', '33', Platform::SMART_LIFE);
$api = new TuyaCloudApi($session);

// METHOD 1
$discovery = new DiscoveryRequest($session);
// Code Integer en retour (@see Sabinus\TuyaCloudApi\Request\Request)
$isSuccess = $discovery->request();
// Récupération de la réponse brute de le requête
$response = $discovery->getResponse();
// Listes des objets découverts
$devices = $discovery->fetchDevices();

// METHOD 2
// Before in v1
$devices = $api->discoverDevices();
// Now in v2
$isSuccess = $api->discoverDevices();
$devices = $api->getAllDevices();

// METHOD 3
$devices = $api->getAllDevices();
~~~


~~~ php
$session = new Session('login', 'password', '33', Platform::SMART_LIFE);
$api = new TuyaCloudApi($session);

// METHOD 1
$query = new QueryRequest($session);
// Code Integer en retour (@see Sabinus\TuyaCloudApi\Request\Request)
$isSuccess = $query->request('QueryDevice', ['devId' => '1234567890']);
// Récupération de la réponse brute de le requête
$response = $discovery->getResponse();
// Récupération des donnees
$datas = $discovery->getDatas();

// METHOD 2
$device = $api->getDeviceById('1234567890')
// Before in v1
$device->update($api);
// Now in v2
$isSuccess = $device->update();
~~~


### Class `ControlRequest`

~~~ php
$session = new Session('login', 'password', '33', Platform::SMART_LIFE);
$api = new TuyaCloudApi($session);

// METHOD 1
$query = new ControlRequest($session);
// Code Integer en retour (@see Sabinus\TuyaCloudApi\Request\Request)
$isSuccess = $query->request('turnOnOff', ['devId' => '1234567890', 'value' => 1]);
// Récupération de la réponse brute de le requête
$response = $discovery->getResponse();

// METHOD 2
$device = $api->getDeviceById('1234567890')
// Before in v1
$device->turnOn($api);
// Now in v2
$isSuccess = $device->turnOn();
~~~


## Class `Device`

Ajout du membre `Session` inclus dans les classes `Device`
~~~ php
$session = new Session('login', 'password', '33', Platform::SMART_LIFE);
$api = new TuyaCloudApi($session);

// Before in v1.x
$device = new SwitchDevice('1234567890');
$device->turnOn($api);
$device->update($api);

// Now in v2.x
$device = new SwitchDevice($session, '1234567890');
$device->turnOn();
$device->update();
~~~

Retour du succès de la commande
~~~ php
$device = new SwitchDevice($session, '1234567890');
$result = $device->turnOn();
echo $result // 0 si OK
~~~


## Class `DeviceEvent`

- Suppression de la classe et des fonctions liées dans les classes `Device`


## Class `CachePool`

- Mettre en cache la réponses des requêtes pour éviter les erreurs aux appels trop fréquents suite aux restrictions de Tuya.
