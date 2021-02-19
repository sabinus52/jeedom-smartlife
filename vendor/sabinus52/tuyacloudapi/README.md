# TuyaCloudApi
Library to control the Tuya device

## Version 2.0

[See release notes](RELEASE-2.md)


## Support devices

- Switch
- Scene
- Light
- Cover
- Climate


## Installation

~~~ bash
composer require sabinus52/tuyacloudapi
~~~


## Example

~~~ php
require __DIR__ . '../vendor/autoload.php';

use Sabinus\TuyaCloudApi\TuyaCloudApi;
use Sabinus\TuyaCloudApi\Session\Session;
use Sabinus\TuyaCloudApi\Session\Platform;
use Sabinus\TuyaCloudApi\Device\DeviceFactory;
use Sabinus\TuyaCloudApi\Device\SwitchDevice;
use Sabinus\TuyaCloudApi\Device\SceneDevice;
use Sabinus\TuyaCloudApi\Device\LightDevice;
use Sabinus\TuyaCloudApi\Device\CoverDevice;
use Sabinus\TuyaCloudApi\Device\ClimateDevice;

$codeReturn = [ 0 => 'OK', 1 => 'IN CACHE', 9 => 'ERROR' ];

/**
 * @param String Identifiant de connexion
 * @param String Mot de passe
 * @param String Code pays
 * @param String Plateforme (tuya ou smartlife)
 * @param Float timeout des requêtes http en secondes
 */
$session = new Session($argv[1], $argv[2], '33', Platform::SMART_LIFE, 5.0);
// FACULTATIF : Change le dossier de stockage du jeton, par défaut dans sys_get_temp_dir()
$session->setFolderStorePool('/tmp');

// Initialize object API
$api = new TuyaCloudApi($session);

// Lance une découverte
$isSuccess = $api->discoverDevices();
var_dump($isSuccess);

// Retourne la liste des objets
$devices = $api->getAllDevices();
//var_dump($devices);


/**
 * Création des objets
 */
// Methode 1 : à partir d'un découverte
$device = $api->getDeviceById('012345678901234598');
// Methode 2
$device = new SwitchDevice($session, '012345678901234598');
// Méthose 3
$device = DeviceFactory::createDeviceFromId($session, '012345678901234598', DeviceFactory::TUYA_SWITCH);


/**
 * Scene
 */
$device = new SceneDevice($session, '012345678901234598');
$device->activate();


/**
 * Prise
 */
$device = new SwitchDevice($session, '012345678901234598');
// Allume la prise
$isSuccess = $device->turnOn();
// Eteins la prise
$isSuccess = $device->turnOff();
// Mets à jour l'objet pour récupérer le dernier état
$isSuccess = $device->update();
print 'Etat : ' . $device->getState();


/**
 * Lampe
 */
$device = new LightDevice($session, '012345678901234598');
// Allume la lampe
$isSuccess = $device->turnOn();
// Change la couleur
$isSuccess = $device->setColor(100, 80);
// Luminosité à 50%
$isSuccess = $device->setBrightness(50);
// Eteins la lampe
$isSuccess = $device->turnOff();
print 'Eteins la lampe : ' . $codeReturn[$isSuccess]; print "\n";


/**
 * Volet
 */
$device = new CoverDevice($session, '012345678901234598');
// Ferme le volet
$isSuccess = $device->close();
// Stoppe le volet
$isSuccess = $device->stop();
// Ouvre le volet
$isSuccess = $device->open();


/**
 * Climatisation
 */
$device = new ClimateDevice($session, '012345678901234598');
// Allume la clim
$isSuccess = $device->turnOn();
// Change la température
$isSuccess = $device->setThermostat();
// Eteins la clim
$isSuccess = $device->open();
~~~

See the file `./test/exemple.php`
