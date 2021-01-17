<?php

require 'bootstrap.php';

use Sabinus\TuyaCloudApi\TuyaCloudApi;
use Sabinus\TuyaCloudApi\Session\Session;
use Sabinus\TuyaCloudApi\Session\Platform;

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

// Retourne la liste des objets
$return = $api->discoverDevices();
var_dump($return);


/**
 * Scene
 */
$device = $api->getDeviceById('1654566545484');
// Active la scene
$device->activate($api);


/**
 * Prise : Méthode 1
 */
$device = $api->getDeviceById('012345678901234598');
// Allume la prise
$rep = $api->sendEvent($device->getTurnOnEvent());
// Mets à jour l'objet pour récupérer le dernier état
$device->update($api);
print 'Etat : ' . $device->getState();


/**
 * Prise : Méthode 2
 */
$device = $api->getDeviceById('012345678901234598');
// Allume la prise
$device->turnOn($api);
sleep(3);
// Eteins la prise
$device->turnOff($api);


/**
 * Lampe
 */
$device = $api->getDeviceById('012345678901234598');
// Allume la lampe
$api->sendEvent($device->getTurnOnEvent());
sleep(3);
// Change la couleur
$api->sendEvent($device->getSetColorEvent(100, 80));
sleep(3);
// Luminosité à 50%
$api->sendEvent($device->getSetBrightnessEvent(50));
sleep(3);
// Eteins la lampe
$api->sendEvent($device->getTurnOffEvent());


/**
 * Volet
 */
$device = $api->getDeviceById('012345678901234598');
// Ferme le volet
$rep = $api->sendEvent($device->getCloseEvent());
sleep(5);
// Stoppe le volet
$rep = $api->sendEvent($device->getStopEvent());
// Ouvre le volet
$rep = $api->sendEvent($device->getOpenEvent());

