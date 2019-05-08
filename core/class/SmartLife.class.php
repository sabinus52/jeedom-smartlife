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
require_once __DIR__  . '/../../3rparty/SmartLifeClient.class.php';


class SmartLife extends eqLogic {
    /*     * *************************Attributs****************************** */



    /*     * ***********************Methode static*************************** */

    public static function searchDevice()
    {
        log::add('SmartLife', 'debug', 'SEARCH DEVICE : Start');
        $user = config::byKey('user', 'SmartLife');
        $password = config::byKey('password', 'SmartLife');
        $country = config::byKey('country', 'SmartLife');
        $api = new SmartLifeClient(new Session($user, $password, $country));

        $result = array();
        $devices = $api->discoverDevices();
        if (!$devices) {
            log::add('SmartLife', 'error', 'Erreur de connexion au cloud Tuya');
            return null;
        }
        foreach ($devices as $device) {
            log::add('SmartLife', 'debug', 'SEARCH DEVICE : Objet "'.$device->getName().'" ('.$device->getId().') trouvé');
            $result[$device->getId()] = $device->getName();
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

    public function postSave() {

        $command = $this->getCmd(null, 'state');
		if (!is_object($command)) {
			$command = new SmartLifeCmd();
		}
		$command->setName(__('Etat', __FILE__));
		$command->setLogicalId('state');
		$command->setEqLogic_id($this->getId());
		$command->setType('info');
        $command->setSubType('binary');
        $command->setOrder(1);
        $command->setDisplay('forceReturnLineAfter', 1);
        $command->save();

        $command = $this->getCmd(null, 'REFRESH');
		if (!is_object($command)) {
			$command = new SmartLifeCmd();
		}
		$command->setName(__('Refresh', __FILE__));
		$command->setLogicalId('REFRESH');
		$command->setEqLogic_id($this->getId());
		$command->setType('action');
        $command->setSubType('other');
        $command->setOrder(2);
        $command->setDisplay('forceReturnLineAfter', 1);
        $command->save();

        $command = $this->getCmd(null, 'ON');
		if (!is_object($command)) {
			$command = new SmartLifeCmd();
		}
		$command->setName(__('Allume', __FILE__));
		$command->setLogicalId('ON');
		$command->setEqLogic_id($this->getId());
		$command->setType('action');
        $command->setSubType('other');
        $command->setOrder(3);
        $command->save();

        $command = $this->getCmd(null, 'OFF');
		if (!is_object($command)) {
			$command = new SmartLifeCmd();
		}
		$command->setName(__('Eteins', __FILE__));
		$command->setLogicalId('OFF');
		$command->setEqLogic_id($this->getId());
		$command->setType('action');
        $command->setSubType('other');
        $command->setOrder(4);
        $command->save();      
        
    }

    public function preUpdate() {
        
    }

    public function postUpdate() {
        
    }

    public function preRemove() {
        
    }

    public function postRemove() {
        
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
        $idDevice = $this->getConfiguration('iddevice');
        $deviceCurrent = new SwitchDevice($idDevice, ''); // TODO mettre le nom

        $user = config::byKey('user', 'SmartLife');
        $password = config::byKey('password', 'SmartLife');
        $country = config::byKey('country', 'SmartLife');
        $api = new SmartLifeClient(new Session($user, $password, $country));

        $devices = $api->discoverDevices();
        foreach ($devices as $device) {
            log::add('SmartLife', 'debug', 'TEST '.$device->getId().' : '.$device->getState());
            if ($device->getId() != $deviceCurrent->getId())
                continue;
            $deviceCurrent->setState($device->getState());
            log::add('SmartLife', 'debug', 'REFRESH de '.$deviceCurrent->getId().' = '.(($deviceCurrent->getState()) ? 'ON' : 'OFF'));
            $this->checkAndUpdateCmd('state', $deviceCurrent->getState());
        }
    }


    public function sendAction($action)
    {
        $user = config::byKey('user', 'SmartLife');
        $password = config::byKey('password', 'SmartLife');
        $country = config::byKey('country', 'SmartLife');
        $api = new SmartLifeClient(new Session($user, $password, $country));
        //log::add('SmartLife', 'debug', "TEST : $user - $password - $country");

        $idDevice = $this->getConfiguration('iddevice');
        $device = new SwitchDevice($idDevice, ''); // TODO mettre le nom
        switch ($action) {
            case 'ON'   : $api->sendEvent($device->getOnEvent()); break;
            case 'OFF'  : $api->sendEvent($device->getOffEvent()); break;
        }
        log::add('SmartLife', 'debug', "ACTION : $action sur l'objet '".$device->getId()."'");
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

    public function execute($_options = array()) {

        $smartlife = $this->getEqLogic();

        $idCommand = $this->getLogicalId();
        log::add('SmartLife', 'debug', "ACTION TEST : $idCommand");

        switch ($idCommand) {
            case 'REFRESH':
                $smartlife->updateInfos();
                break;
            
            case 'ON':
                $smartlife->sendAction('ON');
                break;
            case 'OFF':
                $smartlife->sendAction('OFF');
                break;
        }

        return;
        
    }

    /*     * **********************Getteur Setteur*************************** */
}


