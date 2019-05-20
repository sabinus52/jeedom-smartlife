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


class SmartLife extends eqLogic {
    /*     * *************************Attributs****************************** */



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
     * Recher he les équipements
     * 
     * @return Array of Device
     */
    static public function discoverDevices()
    {
        log::add('SmartLife', 'debug', 'SEARCH DEVICE : Start');
        $api = SmartLife::createTuyaCloudAPI();

        $result = array();
        $devices = $api->discoverDevices();
        if (!$devices) {
            log::add('SmartLife', 'error', 'Erreur de connexion au cloud Tuya');
            return null;
        }
        foreach ($devices as $device) {
            log::add('SmartLife', 'debug', 'SEARCH DEVICE : Objet '.$device->getType().' "'.$device->getName().'" ('.$device->getId().') trouvé');
            $result[$device->getType()][$device->getId()] = [ 'type' => $device->getType(), 'name' => $device->getName() ];
        }
        
        log::add('SmartLife', 'debug', 'SEARCH DEVICE : End');
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



    /*     * *********************Méthodes d'instance************************* */

    public function preInsert() {
        
    }

    public function postInsert() {
        
    }

    public function preSave() {
        
    }

    public function postSave()
    {
        $deviceID = $this->getConfiguration('deviceID');
        if (!$deviceID) return;
        log::add('SmartLife', 'debug', 'SAVE : get id = '.$deviceID);
        $api = SmartLife::createTuyaCloudAPI();
        $api->discoverDevices();
        $device = $api->getDeviceById($deviceID);
        log::add('SmartLife', 'debug', 'SAVE : get device = '.$device->getId().' - '.$device->getType().' - '.$device->getName());

        $configs = SmartLifeConfig::getConfig($device->getType());
        if ($configs) {
            foreach ($configs as $config) {
                $this->addCommand($config);
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
        $api = SmartLife::createTuyaCloudAPI();
        $api->discoverDevices();
        $device = $api->getDeviceById($this->getConfiguration('deviceID'));

        // Mise à jour
        $device->update($api);
        log::add('SmartLife', 'debug', 'REFRESH : '.$device->getId().' '.$device->getName());
        foreach (SmartLifeConfig::getConfigInfos($device->getType()) as $info) {
            $value = call_user_func( array($device, 'get'.ucfirst($info['logicalId'])) );
            $this->checkAndUpdateCmd($info['logicalId'],  $value);
            log::add('SmartLife', 'debug', 'Response '.$info['logicalId'].' = '.$value);
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
        log::add('SmartLife', 'debug', "ACTION EXECUTE : $idCommand");

        switch ($idCommand) {
            case 'REFRESH':
                $smartlife->updateInfos();
                break;
        }

        return;    
    }

    /*     * **********************Getteur Setteur*************************** */
}


