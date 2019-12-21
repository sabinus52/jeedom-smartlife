<?php

/* This file is part of Jeedom.
 *
 * Jeedom is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Jeedom is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Jeedom. If not, see <http://www.gnu.org/licenses/>.
 */

/* * ***************************Includes********************************* */
require_once __DIR__  . '/../../../../core/php/core.inc.php';
require_once __DIR__ . '/../../vendor/autoload.php';
require_once __DIR__ . '/../config/SmartLifeDevice.class.php';

use Sabinus\TuyaCloudApi\TuyaCloudApi;
use Sabinus\TuyaCloudApi\Session\Session;
use Sabinus\TuyaCloudApi\Device\DeviceFactory;
use Sabinus\TuyaCloudApi\Device\Device;
use Sabinus\TuyaCloudApi\Tools\Color;


class SmartLife extends eqLogic {
    /*     * *************************Attributs****************************** */

    static public $api = null;


    /*     * ***********************Methode static*************************** */

    /**
     * Retourne mon API
     * 
     * @return TuyaCloudApi
     */
    static public function createTuyaCloudAPI()
    {
        $user = config::byKey('user', 'SmartLife');
        $password = config::byKey('password', 'SmartLife');
        $country = config::byKey('country', 'SmartLife');
        $platform = config::byKey('platform', 'SmartLife');

        log::add('SmartLife', 'debug', "CONNECTION : $user ($country) $platform");
        return new TuyaCloudApi(new Session($user, $password, $country, $platform));
    }


    /**
     * Teste la connection au Cloud Tuya
     * 
     * @return Boolean
     */
    static public function checkConnection()
    {
        log::add('SmartLife', 'debug', 'CHECK CONNECTION : Start');
        $api = SmartLife::createTuyaCloudAPI();
        try {
            $devices = $api->discoverDevices();
        } catch (Throwable $th) {
            log::add('SmartLife', 'error', 'Erreur de connexion au cloud Tuya : '.$th->getMessage());
            log::add('SmartLife', 'debug', 'CHECK CONNECTION : '.print_r($th, true));
            log::add('SmartLife', 'debug', 'CHECK CONNECTION : End');
            throw new Exception(__($th->getMessage(),__FILE__));
        }
        log::add('SmartLife', 'debug', 'CHECK CONNECTION : OK');
        log::add('SmartLife', 'debug', 'CHECK CONNECTION : End');
        return true;
    }


    /**
     * Recherche les équipements
     * 
     * @param String $mode = Mode de recherche (scantuya | fetch)
     * @return Array of Device
     */
    static public function discoverDevices($mode = '')
    {
        log::add('SmartLife', 'debug', 'SEARCH DEVICE : Start');
        $api = SmartLife::createTuyaCloudAPI();

        // Récupération des équipements déjà existants
        $devicesExisting = array();
        foreach (self::byType('SmartLife') as $eqLogic) {
            $devicesExisting[$eqLogic->getId()] = $eqLogic->getLogicalId();
            log::add('SmartLife', 'debug', 'SEARCH DEVICE : Objet déjà existant -> '.$eqLogic->getName().' ('.$eqLogic->getLogicalId().')');
        }

        // Recherche des équipements depuis le Cloud
        $result = array();
        try {
            $devices = $api->discoverDevices();
        } catch (Throwable $th) {
            log::add('SmartLife', 'error', 'Erreur de connexion au cloud Tuya : '.$th->getMessage());
            log::add('SmartLife', 'debug', 'SEARCH DEVICE : '.print_r($th, true));
            log::add('SmartLife', 'debug', 'SEARCH DEVICE : End');
            event::add('jeedom::alert', array(
				'level' => 'danger',
				'page' => 'SmartLife',
				'message' => __('Erreur de connexion au cloud Tuya : '.$th->getMessage(), __FILE__),
			));
            return null;
        }
        foreach ($devices as $device) {
            // Vérification de l'équipement
            if ( empty($device) || empty($device->getId()) || empty($device->getName()) || empty($device->getType()) ) {
                log::add('SmartLife', 'error', 'CREATE DEVICE : Information manquante pour ajouter l\'équipement : '.print_r($device, true));
                continue;
            }
            $smartlifeDevice = new SmartLifeDevice($device);
            // Si objet reconnu
            if ( $smartlifeDevice->isUnknow() ) {
                log::add('SmartLife', 'debug', 'SEARCH DEVICE : Objet non pris en compte '.print_r($device, true));
                continue;
            }
            if ( $smartlifeDevice->isExistInJeedom() ) {
                log::add('SmartLife', 'debug', 'SEARCH DEVICE : Objet trouvé "'.$device->getName().'" ('.$device->getId().') de type \''.$device->getType().'\'');
            } else {
                log::add('SmartLife', 'debug', 'SEARCH DEVICE : Nouvel objet trouvé "'.$device->getName().'" ('.$device->getId().') de type \''.$device->getType().'\'');
            }
            if ( $mode == 'scanTuya' ) {
                $isNew = $smartlifeDevice->createEqLogic();
                if ( $isNew ) {
                    event::add('jeedom::alert', array(
				        'level' => 'warning',
				        'page' => 'SmartLife',
				        'message' => __('Objet ajouté avec succès "'.$device->getName().'" de type "'.$device->getType().'"', __FILE__),
                    ));
                }
            }
            $result[$device->getId()] = [ 'type' => $device->getType(), 'name' => $device->getName() ];
        }

        // Sauvegarde la liste dans la configuration Jeedom
        config::save('devices', serialize($result), 'SmartLife');
        log::add('SmartLife', 'debug', 'SEARCH DEVICE : End');

        return $result;
    }


    /**
     * Retourne la liste des equipements par type pour la listebox
     * 
     * @return Array
     */
    static public function getDevicesByType()
    {
        // Récupération des équipements depuis la configuration Jeedom
        $devices = config::byKey('devices', 'SmartLife');
        if (empty($devices)) {
            $devices = self::discoverDevices('fetch');
        } else {
            $devices = unserialize($devices);
        }

        // Regroupement par type
        $result = array();
        foreach ($devices as $id => $device) {
            $result[$device['type']][$id] = $device;
        }
        return $result;
    }



    /*
     * Fonction exécutée automatiquement toutes les minutes par Jeedom
      public static function cron() {

      }
     */


    /*
     * Fonction exécutée automatiquement toutes les heures par Jeedom
      public static function cronHourly() {

      }
     */

    /*
     * Fonction exécutée automatiquement tous les jours par Jeedom
      public static function cronDaily() {

      }
     */


    /**
     * Mise à jour des statuts de tous les objets
     * 
     * @see cron::byClassAndFunction('SmartLife', 'updateAll') 
     */
    public static function updateAll()
    {
        log::add('SmartLife', 'debug', 'UPDATE : Start');
    
        $api = SmartLife::createTuyaCloudAPI();
        try {
            $result = $api->discoverDevices();
            log::add('SmartLife', 'debug', 'UPDATE : Découverte de '.count($result).' devices');
        } catch (Throwable $th) {
            log::add('SmartLife', 'error', 'UPDATE : Erreur de connexion au cloud Tuya : '.$th->getMessage());
            log::add('SmartLife', 'debug', 'UPDATE : '.print_r($th, true));
            log::add('SmartLife', 'debug', 'UPDATE : End');
            return null;
        }

		foreach (self::byType('SmartLife') as $eqLogic) {
            $deviceID = $eqLogic->getLogicalId();
            $device = $api->getDeviceById($deviceID);
            if ($device == null) {
                log::add('SmartLife', 'debug', 'UPDATE '.$deviceID.' : '.$eqLogic->getName().' non trouvé lors de la récupération des status');
                continue;
            }
            if ($device->getType() == DeviceFactory::TUYA_SCENE) continue;
            log::add('SmartLife', 'debug', 'UPDATE '.$deviceID.' : '.$eqLogic->getName());
			if ($eqLogic->getIsEnable() == 0) {
                log::add('SmartLife', 'debug', 'UPDATE '.$deviceID.' : Non activé -> PAS DE MISE à JOUR');
				continue;
            }
            log::add('SmartLife', 'debug', 'UPDATE '.$deviceID.' : '.print_r($device, true));

            $eqLogic->update($device);
            
        }
    
        log::add('SmartLife', 'debug', 'UPDATE : End');
    }


    /*     * *********************Méthodes d'instance************************* */

    public function preInsert() {
        
    }

    public function postInsert() {
        
    }

    public function preSave()
    {
        // Uniquement pour les anciens objets créés manuellement
        // mets à jour le LogicalID pour éviter les doublons lors du scan
        if ( empty($this->getLogicalId()) ) { // || ( $this->getLogicalId() !=  $this->getConfiguration('deviceID') )
            $deviceID = $this->getConfiguration('deviceID');
            log::add('SmartLife', 'info', 'PRESAVE '.$deviceID.' : Changement ID de "'.$this->getLogicalId().'" vers "'.$deviceID.'"');
            $api = SmartLife::createTuyaCloudAPI();
            try {
                $api->discoverDevices();
            } catch (Throwable $th) {
                log::add('SmartLife', 'error', 'Erreur de connexion au cloud Tuya : '.$th->getMessage());
                log::add('SmartLife', 'debug', 'PRESAVE : '.print_r($th, true));
                throw new Exception(__('Erreur de connexion au cloud Tuya : '.$th->getMessage(),__FILE__));
            }
            log::add('SmartLife', 'debug', 'PRESAVE '.$deviceID.' : SET LogicalId');
            $this->setLogicalId($deviceID);
            $device = $api->getDeviceById($deviceID);
            log::add('SmartLife', 'debug', 'PRESAVE '.$deviceID.' : SET deviceType = '.$device->getType());
            $this->setConfiguration('deviceType', $device->getType());
            log::add('SmartLife', 'debug', 'PRESAVE '.$deviceID.' : SET device = '.print_r($device, true));
            $this->setConfiguration('device', serialize($device));
        }

    }

    public function postSave()
    {

    }

    public function preUpdate()
    {

    }

    public function postUpdate() {
        
    }

    public function preRemove() {
        
    }

    public function postRemove() {
        
    }


    /**
     * Ajout des commandes à Jeedom
     * 
     * @param Array $config : Configuration de la commande
     */
    public function addCommand(Array $config)
    {
        $cmdDevice = $this->getCmd(null, $config['logicalId']);
        if ( !is_object($cmdDevice) ) {

            $cmdDevice = new SmartLifeCmd();
            $cmdDevice->setName(__($config['name'], __FILE__));
            $cmdDevice->setLogicalId( $config['logicalId'] );
            $cmdDevice->setEqLogic_id( $this->getId() );

            if ( isset($config['display']) ) {
                foreach ($config['display'] as $key => $value) {
                    $cmdDevice->setDisplay($key, $value);
                }
                unset($config['display']);
            }

            // Assigne les paramètres du JSON à chaque fonction de l'eqLogic
            utils::a2o($cmdDevice, $config);
            log::add('SmartLife', 'debug', 'CREATE DEVICE '.$this->getLogicalId().' : ADD COMMAND '.$config['logicalId']);
        }

        // Ne doit pas être changé
        $cmdDevice->setType( $config['type'] );
        $cmdDevice->setSubType( $config['subType'] );
        if (isset($config['value'])) {
            foreach ($this->getCmd() as $eqLogic_cmd) {
				if ($config['value'] == $eqLogic_cmd->getLogicalId()) {
					$cmdDevice->setValue($eqLogic_cmd->getId());
				}
			}
        }

        // Sauvegarde
        $cmdDevice->save();
    }

    /*
     * Non obligatoire mais permet de modifier l'affichage du widget si vous en avez besoin
      public function toHtml($_version = 'dashboard') {

      }
     */

    /*
     * Non obligatoire mais ca permet de déclencher une action après modification de variable de configuration
    public static function postConfig_<Variable>() {
    }
     */

    /*
     * Non obligatoire mais ca permet de déclencher une action avant modification de variable de configuration
    public static function preConfig_<Variable>() {
    }
     */

    /*     * **********************Getteur Setteur*************************** */

    /**
     * Mets à jour les infos de l'objet
     * 
     * @param Device $device
     */
    private function update(Device $device)
    {
        $deviceID = $this->getLogicalId();
        $cmdInfos = unserialize($this->getConfiguration('deviceCmdInfos'));
        $smartlifeDevice = new SmartLifeDevice($device);
        foreach ($cmdInfos as $info) {
            // FIXME : API tuya ne retourne plus le statut sur la couleur et la température
            if ($info == 'TEMPERATURE' || $info == 'COLORHUE' || $info == 'SATURATION') continue;
            $value = $smartlifeDevice->getValueCommandInfo($info);
            log::add('SmartLife', 'debug', 'UPDATE '.$deviceID.' : checkAndUpdateCmd '.$info.' = '.$value);
            $this->checkAndUpdateCmd($info, $value);
        }
        $this->setConfiguration('device', serialize($device));
        $this->save(true);
    }


    /**
     * Lance l'action de rafraichissement des infos sur un objet
     */
    public function refresh()
    {
        if ( !SmartLife::$api) SmartLife::$api = SmartLife::createTuyaCloudAPI();
        $device = unserialize($this->getConfiguration('tuya'));
        log::add('SmartLife', 'info', 'REFRESH '.$this->getLogicalId().' : '.$this->getName());
        log::add('SmartLife', 'debug', 'REFRESH '.$this->getLogicalId().' : '.print_r($device, true));

        // Mise à jour avec 3 tentatives
        $smartlifeDevice = new SmartLifeDevice($device);
        $smartlifeDevice->callFunctionEvent(SmartLife::$api, 'update', array(), 'REFRESH');
        log::add('SmartLife', 'debug', 'REFRESH '.$this->getLogicalId().' : '.print_r($device, true));

        $this->update($device);
    }


    /**
     * Lance une commande sur l'objet
     * 
     * @param String $action : Action à éxécuter = fonction API de l'objet 
     * @param Array $params : Paramètres de l'action
     */
    public function sendAction($action, array $params = array())
    {
        if ( !SmartLife::$api) SmartLife::$api = SmartLife::createTuyaCloudAPI();
        $device = unserialize($this->getConfiguration('tuya'));
        log::add('SmartLife', 'debug', 'SEND EVENT '.$this->getLogicalId().' : '.$this->getName());
        log::add('SmartLife', 'debug', 'SEND EVENT '.$this->getLogicalId().' : '.print_r($device, true));
        log::add('SmartLife', 'info',  'SEND EVENT '.$this->getLogicalId().' : '.$action.'('.implode(',', $params).')');

        // Exécution ce l'énènement de l'action
        $smartlifeDevice = new SmartLifeDevice($device);
        $smartlifeDevice->callFunctionEvent(SmartLife::$api, $action, $params, 'SEND EVENT');

        sleep(3);
        $this->refresh();
    }

}

class SmartLifeCmd extends cmd {
    /*     * *************************Attributs****************************** */


    /*     * ***********************Methode static*************************** */


    /*     * *********************Methode d'instance************************* */

    /*
     * Non obligatoire permet de demander de ne pas supprimer les commandes même si elles ne sont pas dans la nouvelle configuration de l'équipement envoyé en JS
      public function dontRemoveCmd() {
      return true;
      }
     */

    public function execute($_options = array())
    {
        $smartlife = $this->getEqLogic();

        $idCommand = $this->getLogicalId();
        log::add('SmartLife', 'debug', "ACTION EXECUTE : $idCommand ".print_r($_options, true));

        switch ($idCommand) {
            case 'REFRESH':
                $smartlife->refresh();
                break;
            default:
                if (isset($_options['slider'])) {
                    $smartlife->sendAction($idCommand, array($_options['slider']));
                } elseif (isset($_options['color'])) {
                    $ret = Color::hexToHsl($_options['color']);
                    $smartlife->sendAction($idCommand, array(round($ret['H']), round($ret['S'])) );
                } else {
                    $smartlife->sendAction($idCommand);
                }
                break;
        }

        return;    
    }

    /*     * **********************Getteur Setteur*************************** */
}


