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
require_once __DIR__ . '/../config/SmartLifeLog.class.php';
require_once __DIR__ . '/../config/SmartLifeConfig.class.php';
require_once __DIR__ . '/../config/SmartLifeDiscovery.class.php';
require_once __DIR__ . '/../config/SmartLifeDevice.class.php';

use Sabinus\TuyaCloudApi\TuyaCloudApi;
use Sabinus\TuyaCloudApi\Session\Session;
use Sabinus\TuyaCloudApi\Device\DeviceFactory;
use Sabinus\TuyaCloudApi\Device\Device;
use Sabinus\TuyaCloudApi\Request\Request;
use Sabinus\TuyaCloudApi\Tools\Color;


class SmartLife extends eqLogic {
    /*     * *************************Attributs****************************** */

    static private $sessionCloudTuya = null;


    /**
     * @var Device
     */
    private $device;


    /*     * ***********************Methode static*************************** */

    /**
     * Retourne mon API
     * 
     * @return TuyaCloudApi
     */
    static public function getSessionTuya()
    {
        if ( self::$sessionCloudTuya == null ) {
            $user = config::byKey('user', 'SmartLife');
            $password = config::byKey('password', 'SmartLife');
            $country = config::byKey('country', 'SmartLife');
            $platform = config::byKey('platform', 'SmartLife');
            $timeout = (config::byKey('timeout', 'SmartLife') != '') ? config::byKey('timeout', 'SmartLife') : '5';

            log::add('SmartLife', 'debug', "NEW SESSION : $user ($country) $platform - $timeout s");
            self::$sessionCloudTuya = new Session($user, $password, $country, $platform, $timeout);
        }
        return self::$sessionCloudTuya;
    }


    /**
     * Teste la connection au Cloud Tuya
     * 
     * @return Boolean
     */
    static public function checkConnection()
    {
        SmartLifeLog::begin('CHECK CONNECTION');
        $session = SmartLife::getSessionTuya();
        $api = new TuyaCloudApi($session);
        try {
            $result = $api->checkConnection();
        } catch (Throwable $th) {
            $result = false;
        }
        if ( !$result ) {
            SmartLifeLog::exception('CHECK CONNECTION', $th);
            SmartLifeLog::end('CHECK CONNECTION');
            throw new Exception(__($th->getMessage(),__FILE__));
        }
        SmartLifeLog::info('CHECK CONNECTION', ($result) ? 'OK' : 'ERROR');
        SmartLifeLog::end('CHECK CONNECTION');
        return true;
    }


    /**
     * Recherche les équipements sur clique du bouton
     * 
     * @return Array of Device
     */
    static public function discoverDevices()
    {
        SmartLifeLog::begin('DISCOVERY');
        $session = SmartLife::getSessionTuya();
        $api = new TuyaCloudApi($session);

        // Recherche des équipements depuis le Cloud
        $result = array();
        try {
            $result = $api->discoverDevices();
            $devices = $api->getAllDevices();
            SmartLifeLog::debug('DISCOVERY', 'TuyaCloudApi::discoverDevices()', $result);
            SmartLifeLog::info('DISCOVERY', 'Découverte de '.count($devices).' devices');
        } catch (Throwable $th) {
            SmartLifeLog::exception('DISCOVERY', $th);
            SmartLifeLog::end('DISCOVERY');
            event::add('jeedom::alert', array(
				'level' => 'danger',
				'page' => 'SmartLife',
				'message' => __('Erreur de connexion au cloud Tuya : '.$th->getMessage(), __FILE__),
			));
            return null;
        }

        // Pour chaque objets trouvés
        foreach ($devices as $device) {
            $discover = new SmartLifeDiscovery($device);
            $discover->execute();
        }

        SmartLifeLog::end('DISCOVERY');
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
        SmartLifeLog::begin('UPDATE');
    
        $session = SmartLife::getSessionTuya();
        $api = new TuyaCloudApi($session);
        try {
            $result = $api->discoverDevices();
            SmartLifeLog::debug('DISCOVERY', 'TuyaCloudApi::discoverDevices()', $result);
            SmartLifeLog::info('UPDATE', 'Découverte de '.count($api->getAllDevices()).' devices', $result);
        } catch (Throwable $th) {
            SmartLifeLog::exception('UPDATE', $th);
            SmartLifeLog::end('UPDATE');
            return null;
        }

        // Si En oache ou en erreur alors pas de mises à jour
        if ( $result != Request::RETURN_SUCCESS ) {
            SmartLifeLog::info('UPDATE', 'Aucun retour du Cloud Tuya -> pas de mise à jour effectuée');
            SmartLifeLog::end('UPDATE');
            return null;
        }

        //  Pour chaque objet enregistré dans Jeedom
        foreach (self::byType('SmartLife') as $eqLogic) {
            // Récupère l'objet depuis la découverte
            $deviceID = $eqLogic->getLogicalId();
            $device = $api->getDeviceById($deviceID);
            if ($device == null) {
                $device = DeviceFactory::createDeviceFromId($session, $eqLogic->getLogicalId(), $eqLogic->getConfiguration('tuyaType'));
                SmartLifeLog::header('UPDATE', $device, 'Non trouvé lors de la découverte');
                continue;
            }
            SmartLifeLog::header('UPDATE', $device);
            //  Pas de mise à jour pour les scènes
            if ($device->getType() == DeviceFactory::TUYA_SCENE) {
                SmartLifeLog::debug('UPDATE', $device, 'type=scene -> PAS DE MISE A JOUR');
                continue;
            }
            // Pas de mise à jour pour les objets non activés
			if ($eqLogic->getIsEnable() == 0) {
                SmartLifeLog::debug('UPDATE', $device, 'enable=false -> PAS DE MISE A JOUR');
				continue;
            }

            // Mise à jour de l'eqLogic
            $eqLogic->update($device);
            
        }
    
        SmartLifeLog::end('UPDATE');
    }


    /*     * *********************Méthodes d'instance************************* */

    public function preInsert() {
        
    }

    public function postInsert() {
        
    }

    public function preSave()
    {

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
    public function addCommand(Array $config, Device $device)
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
            SmartLifeLog::debug('DISCOVERY', $device, 'ADD COMMAND '.$config['logicalId']);
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
        $smartlifeDevice = new SmartLifeDevice($device);

        // Récupère les commandes de type "info"
        $cmdInfos = cmd::byEqLogicId($this->getId(), 'info');
        foreach ($cmdInfos as $cmd) {

            // Récupère la valeur
            $value = $smartlifeDevice->getValueCommandInfo( $cmd->getLogicalId() );

            // Mise à jour de la commande
            if ( is_null($value) ) {
                $message = '(non mis à jour)';
            } else {
                $this->checkAndUpdateCmd($cmd->getLogicalId(), $value);
                $message = '= '.$value;
            }
            SmartLifeLog::debug('UPDATE', $device, $cmd->getLogicalId().' '.$message);
        }

        $this->setConfiguration('tuyaData', $device->getData());
        $this->save(true);
    }


    /**
     * Lance l'action de rafraichissement des infos sur un objet
     */
    public function refresh()
    {
        $this->sendAction('update');
    }


    /**
     * Lance une commande sur l'objet
     * 
     * @param String $action : Action à éxécuter = fonction API de l'objet 
     * @param Array $params : Paramètres de l'action
     */
    public function sendAction($action, array $params = array())
    {
        $session = SmartLife::getSessionTuya();
        $device = DeviceFactory::createDeviceFromId($session, $this->getLogicalId(), $this->getConfiguration('tuyaType'), $this->getConfiguration('tuyaName'));
        $device->setData( $this->getConfiguration('tuyaData') );
        SmartLifeLog::header('SEND EVENT', $device);
        
        // Exécution ce l'énènement de l'action
        $smartlifeDevice = new SmartLifeDevice($device);
        $result = $smartlifeDevice->callFunctionEvent($action, $params, 'SEND EVENT');
        SmartLifeLog::debug('SEND EVENT', $device, $action.'('.implode(',', $params).')', $result);
        SmartLifeLog::debugData('SEND EVENT', $device);

        // Mise à jour des infos
        if ( $result != Request::RETURN_SUCCESS ) {
            SmartLifeLog::info('SEND EVENT', $device, 'Aucun retour du Cloud Tuya -> pas de mise à jour effectuée');
        } else {
            $this->update($device);
        }
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


