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
require_once __DIR__  . '/../config/SmartLife.config.php';

use Sabinus\TuyaCloudApi\TuyaCloudApi;
use Sabinus\TuyaCloudApi\Session\Session;
use Sabinus\TuyaCloudApi\Device\DeviceFactory;
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
        $devices = $api->discoverDevices();
        if (!$devices) {
            log::add('SmartLife', 'error', 'Erreur de connexion au cloud Tuya');
            return null;
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
            $devicesExisting[$eqLogic->getId()] = $eqLogic->getConfiguration('deviceID');
            log::add('SmartLife', 'debug', 'SEARCH DEVICE : Objet déjà existant -> '.$eqLogic->getName().' ('.$eqLogic->getConfiguration('deviceID').')');
        }

        // Recherche des équipements depuis le Cloud
        $result = array();
        $devices = $api->discoverDevices();
        if (!$devices) {
            log::add('SmartLife', 'error', 'Erreur de connexion au cloud Tuya');
            return null;
        }
        foreach ($devices as $device) {
            if ($device == null) {
                log::add('SmartLife', 'debug', 'SEARCH DEVICE : Objet non pris en compte '.print_r($device, true)); // TODO LOG inconnu
                continue;
            }
            if (in_array($device->getId(), $devicesExisting)) {
                log::add('SmartLife', 'debug', 'SEARCH DEVICE : Objet trouvé "'.$device->getName().'" ('.$device->getId().') de type \''.$device->getType().'\'');
            } else {
                log::add('SmartLife', 'debug', 'SEARCH DEVICE : Nouvel objet trouvé "'.$device->getName().'" ('.$device->getId().') de type \''.$device->getType().'\'');
            }
            if ($mode == 'scanTuya') self::createDevice($device);
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


    /**
     * Retourne les infos du device depuis la configuration Jeedom
     * 
     * @param String $id : Identifiant Tuya du device
     * @return Array
     */
    static public function getDeviceInfos($id)
    {
        // Récupération des équipements depuis la configuration Jeedom
        $devices = config::byKey('devices', 'SmartLife');
        if (empty($devices)) {
            $devices = self::discoverDevices('fetch');
        } else {
            $devices = unserialize($devices);
        }

        return ( isset($devices[$id]) ) ? $devices[$id] : null;
    }


    /**
     * Crée l'objet Jeedom de l'équipement trouvé syr le Cloud Tuya
     * 
     * @param Device $device
     * @return SmartLife
     */
    public static function createDevice($device)
    {
		event::add('jeedom::alert', array(
			'level' => 'warning',
			'page' => 'SmartLife',
			'message' => __('Nouvel objet trouvé', __FILE__),
        ));
        log::add('SmartLife', 'info', 'CREATE DEVICE : Objet en cours d\'inclusion "'.$device->getName().'" ('.$device->getId().') de type \''.$device->getType().'\'');
        
        // Vérification
		if ( empty($device) || empty($device->getId()) || empty($device->getName()) || empty($device->getType()) ) {
			log::add('SmartLife', 'error', 'CREATE DEVICE : Information manquante pour ajouter l\'équipement : '.print_r($device, true));
			event::add('jeedom::alert', array(
				'level' => 'danger',
				'page' => 'SmartLife',
				'message' => __('Information manquante pour ajouter l\'équipement. Inclusion impossible', __FILE__),
			));
			return false;
        }

		$logicalID = $device->getId();
        $smartlife = self::byLogicalId($logicalID, 'SmartLife');
        // Créer le nouvel équipement s'il n'existe pas
		if ( !is_object($smartlife) ) {
			$smartlife = new SmartLife();
			$smartlife->setEqType_name('SmartLife');
            $smartlife->setLogicalId($logicalID);
            $smartlife->setName($device->getName() . ' ' . $logicalID);           
			event::add('jeedom::alert', array(
				'level' => 'warning',
				'page' => 'SmartLife',
				'message' => __('Objet ajouté avec succès "'.$device->getName().'" de type "'.$device->getType().'"', __FILE__),
            ));
        }

        $smartlife->setConfiguration('deviceID', $logicalID);
        $smartlife->setConfiguration('deviceType', $device->getType());
        $smartlife->setConfiguration('device', serialize($device));
        if ($device->isOnline()) {
			$smartlife->setIsEnable(1);
            $smartlife->setIsVisible(1);
        } else {
            $smartlife->setIsEnable(0);
            $smartlife->setIsVisible(0);
        }

        // Sauvegarde
        $smartlife->save();
        log::add('SmartLife', 'info', 'CREATE DEVICE : Objet ajouté avec succès "'.$device->getName().'" ('.$device->getId().') de type \''.$device->getType().'\'');

		return $smartlife;
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



    /*     * *********************Méthodes d'instance************************* */

    public function preInsert() {
        
    }

    public function postInsert() {
        
    }

    public function preSave()
    {
        // Uniquement pour les anciens objets créés manuellement
        // mets à jour le LogicalID pour éviter les doublons lors du scan
        // OU
        // Si changement d'ID de l'équipement
        if ( empty($this->getLogicalId()) || ( $this->getLogicalId() !=  $this->getConfiguration('deviceID') ) ) {
            $deviceID = $this->getConfiguration('deviceID');
            log::add('SmartLife', 'info', 'PRESAVE '.$deviceID.' : Changement ID de "'.$this->getLogicalId().'" vers "'.$deviceID.'"');
            $api = SmartLife::createTuyaCloudAPI();
            $api->discoverDevices();
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
        $deviceID = $this->getConfiguration('deviceID');
        if (!$deviceID) return;
        $deviceType = $this->getConfiguration('deviceType');
        log::add('SmartLife', 'debug', 'POSTSAVE : get id = '.$deviceID);
        log::add('SmartLife', 'debug', 'POSTSAVE : get type = '.$deviceType);

        $configs = SmartLifeConfig::getConfig($deviceType);
        if ($configs) {
            foreach ($configs as $config) {
                $this->addCommand($config);
                log::add('SmartLife', 'debug', 'POSTSAVE : set command '.$deviceID.' = '.$config['logicalId']);
            }
        } else {
            throw new Exception(__('Type d\'équipement non pris en charge',__FILE__));
        }
    }

    public function preUpdate()
    {
        if (!$this->getConfiguration('deviceID')) {
            throw new Exception(__('Merci de choisir un équipement.',__FILE__));	
        }
        // TODO alerte si changement
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
        }
        $cmdDevice->setName(__($config['name'], __FILE__));
        $cmdDevice->setLogicalId( $config['logicalId'] );
        $cmdDevice->setEqLogic_id( $this->getId() );
        $cmdDevice->setType( $config['type'] );
        $cmdDevice->setSubType( $config['subType'] );
        $cmdDevice->setOrder( $config['order'] );
        if (isset($config['icon'])) $cmdDevice->setDisplay( 'icon', '<i class="fa '.$config['icon'].'"></i>' );
        if (isset($config['forceReturnLineAfter'])) $cmdDevice->setDisplay( 'forceReturnLineAfter', $config['forceReturnLineAfter'] );
        if (isset($config['unity'])) $cmdDevice->setUnite( $config['unity'] );
        if (isset($config['value'])) {
            foreach ($this->getCmd() as $eqLogic_cmd) {
				if ($config['value'] == $eqLogic_cmd->getLogicalId()) {
					$cmdDevice->setValue($eqLogic_cmd->getId());
				}
			}
        }
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

    public function updateInfos()
    {
        log::add('SmartLife', 'debug', '=== REFRESH ==================================================');
        if ( !SmartLife::$api) SmartLife::$api = SmartLife::createTuyaCloudAPI();
        $device = unserialize($this->getConfiguration('device'));

        // Mise à jour
        $device->update(SmartLife::$api);
        log::add('SmartLife', 'debug', 'REFRESH : '.$device->getId().' '.$device->getName());
        foreach (SmartLifeConfig::getConfigInfos($device->getType()) as $info) {
            
            if ($info['logicalId'] == SmartLifeConfig::COLORHUE) {
                $value = array('H' => $device->getColorHue(), 'S' => $device->getColorSaturation(), 'L' => $device->getBrightness());
                log::add('SmartLife', 'debug', 'Response '.$info['logicalId'].' = '.print_r($value, true));
                log::add('SmartLife', 'debug', 'Response '.$info['logicalId'].' = '.Color::hslToHex($value));
                $this->checkAndUpdateCmd($info['logicalId'], '#'.Color::hslToHex($value));
            } else {
                $value = call_user_func( array($device, 'get'.ucfirst($info['logicalId'])) );
                $this->checkAndUpdateCmd($info['logicalId'], $value);
                log::add('SmartLife', 'debug', 'Response '.$info['logicalId'].' = '.$value);
            }
            
        }
        $this->setConfiguration('device', serialize($device));
        $this->save(true);
    }


    public function sendAction($action, $value1 = null, $value2 = null)
    {
        if ( !SmartLife::$api) SmartLife::$api = SmartLife::createTuyaCloudAPI();
        $device = unserialize($this->getConfiguration('device'));
        log::add('SmartLife', 'debug', 'SEND EVENT '.$this->getLogicalId().' : '.$this->getName());
        log::add('SmartLife', 'debug', 'SEND EVENT '.$this->getLogicalId().' : '.print_r($device, true));
        log::add('SmartLife', 'info',  'SEND EVENT '.$this->getLogicalId().' : '.$action.'('.$value1.','.$value2.')');

        SmartLife::$api->sendEvent( call_user_func( array($device, 'get'.$action.'Event'), $value1, $value2 ) );

        sleep(3);
        $this->updateInfos();
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
                $smartlife->updateInfos();
                break;
            default:
                if (isset($_options['slider'])) {
                    $smartlife->sendAction($idCommand, $_options['slider']);
                } elseif (isset($_options['color'])) {
                    $ret = Color::hexToHsl($_options['color']);
                    $smartlife->sendAction($idCommand, round($ret['H']), round($ret['S']) );
                } else {
                    $smartlife->sendAction($idCommand);
                }
                break;
        }

        return;    
    }

    /*     * **********************Getteur Setteur*************************** */
}


