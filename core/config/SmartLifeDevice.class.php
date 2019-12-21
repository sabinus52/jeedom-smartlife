<?php
/**
 * Classe d'un équipement SmartLife
 */

use Sabinus\TuyaCloudApi\TuyaCloudApi;
use Sabinus\TuyaCloudApi\Device\Device;
use Sabinus\TuyaCloudApi\Device\DeviceFactory;
use Sabinus\TuyaCloudApi\Tools\Color;


class SmartLifeDevice
{

    const FILE_CONFIG = '/../config/%s/%s.json';


    /**
     * Objet du device retourné par l'API
     * 
     * @param Device
     */
    private $device;


    /**
     * Si nouvel équipement créé
     * 
     * @param Boolean
     */
    private $isNew;



    /**
     * Constructeur
     * 
     * @param Device $device
     */
    public function __construct(Device $device)
    {
        $this->device = $device;
        $this->isNew = false;
    }


    /**
     * Si device connu par mon API
     * 
     * @return Boolean
     */
    public function isUnknow()
    {
        return ( $this->device->getType() == DeviceFactory::TUYA_UNKNOWN );
    }


    /**
     * Si équipement déjà créé dans Jeedom
     * 
     * @return Boolean
     */
    public function isExistInJeedom()
    {
        $smartlife = SmartLife::byLogicalId($this->device->getId(), 'SmartLife');
        return ( is_object($smartlife) );
    }


    /**
     * Appele la fonction d'évènement pour un équipement SamrtLife
     * 
     * @param TuyaCloudApi $api
     * @param String $functionName : Nom de la fonction de l'évènement
     * @param Array $params : Paramètres de la fonction $functionName
     * @param String $msgLog : Message de log en rapport à la fonction
     */
    public function callFunctionEvent(TuyaCloudApi $api, $functionName, array $params, $msgLog)
    {
        $retry = 3;
        while ($retry > 0) {
            $retry--;
            log::add('SmartLife', 'debug', $msgLog.' : tentative '.(3-$retry));
            try {
                switch ( count($params) ) {
                    case 3 : $this->device->$functionName($api, $params[0], $params[1], $params[2]); break;
                    case 2 : $this->device->$functionName($api, $params[0], $params[1]); break;
                    case 1 : $this->device->$functionName($api, $params[0]); break;
                    case 0 : $this->device->$functionName($api); break;
                }
                $retry = 0;
            } catch (Throwable $th) {
                log::add('SmartLife', 'debug', 'Erreur de connexion au cloud Tuya : '.$th->getMessage());
                if ($retry > 0) continue;
                log::add('SmartLife', 'debug', $msgLog.' : '.print_r($th, true));
                log::add('SmartLife', 'error', 'Erreur de connexion au cloud Tuya : '.$th->getMessage());
                throw new Exception(__('Erreur de connexion au cloud Tuya : '.$th->getMessage(),__FILE__));
            }
        }
    }


    /**
     * Retourne la valeur d'un paramètre d'un équipement Tuya
     * 
     * @param String $cmdInfo : Nom de la commande info = nom du paramètre Tuya
     * @param String|Integer
     */
    public function getValueCommandInfo($cmdInfo)
    {
        switch ($cmdInfo) {
            case 'COLORHUE':
                $value = array('H' => $this->device->getColorHue(), 'S' => $this->device->getColorSaturation(), 'L' => $this->device->getBrightness());
                return  '#'.Color::hslToHex($value);
                break;
            case 'STATE' :
                $value = $this->device->getState();
                if ( $this->device->getType() == DeviceFactory::TUYA_COVER ) {
                    switch ($value) {
                        case 3 : return 1; // Entre ouvert
                        case 2 : return 0; // Fermé
                        case 1 : return 2; // Ouvert
                        default: return $value;
                    }
                } else {
                    return $value;
                }
                break;
            default:
                $functionName = 'get'.ucfirst($cmdInfo);
                return $this->device->$functionName();
                break;
        }
    }


    /**
     * Crée l'objet Jeedom de l'équipement trouvé sur le Cloud Tuya
     * 
     * @return SmartLife
     */
    public function createEqLogic()
    {
        log::add('SmartLife', 'info', 'CREATE DEVICE '.$this->device->getId().': Objet en cours d\'inclusion "'.$this->device->getName().'" de type \''.$this->device->getType().'\'');

        // Vérification de la configuration de l'équipement
        $configs = $this->loadJSON();
        if ( ! $configs ) {
            log::add('SmartLife', 'error', 'CREATE DEVICE '.$this->device->getId().' : Type d\'équipement non pris en charge "'.$this->device->getType().'" pour "'.$this->device->getName().'"');
            return false;
        }

        // Récupère l'instance de l'objet Jeedom à créer
        $smartlife = $this->getInstanceEqLogicSmartLife($configs['commands']);

        // Création des commandes
        $this->createCommands($smartlife, $configs['commands']);

        log::add('SmartLife', 'info', 'CREATE DEVICE '.$this->device->getId().' : Objet ajouté avec succès "'.$this->device->getName().'" ('.$this->device->getId().') de type \''.$this->device->getType().'\'');
		return $this->isNew;
	}


    /**
     * Retourne l'instance de l'objet Jeedom à créer
     * 
     * @param Array $commands : Liste des commandes
     * @return SmartLife
     */
    private function getInstanceEqLogicSmartLife(array $commands)
    {
        // Recherche si l'objet existe déjà
        $smartlife = SmartLife::byLogicalId($this->device->getId(), 'SmartLife');

        // Créer le nouvel équipement s'il n'existe pas
		if ( !is_object($smartlife) ) {
            $this->isNew = true;
			$smartlife = new SmartLife();
			$smartlife->setEqType_name('SmartLife');
            $smartlife->setLogicalId($this->device->getId());
            $smartlife->setName($this->device->getName() . ' ' . $this->device->getId());
        }

        // Affecte la configuration du device
        $smartlife->setConfiguration('tuyaID', $this->device->getId());
        $smartlife->setConfiguration('tuyaType', $this->device->getType());
        $smartlife->setConfiguration('tuyaName', $this->device->getName());
        $smartlife->setConfiguration('tuya', serialize($this->device));

        // Enregistre les commandes de type "infos" pour la mise à jour des états
        $cmdInfos = array();
        foreach ($commands as $command) {
            if ( $command['type'] == 'info' ) $cmdInfos[] = $command['logicalId'];
        }
        $smartlife->setConfiguration('deviceCmdInfos', serialize($cmdInfos));

        // Désactive si l'objet n'est plus en ligne
        if ($this->device->isOnline()) {
			$smartlife->setIsEnable(1);
            $smartlife->setIsVisible(1);
        } else {
            $smartlife->setIsEnable(0);
            $smartlife->setIsVisible(0);
        }
        
        // Sauvegarde et retour de l'objet
        $smartlife->save(true);
        return $smartlife;
    }


    /**
     * Crée les commandes de l'objet EqLogic de type SmartLife
     * 
     * @param SmartLife $smartlife : EqLogic de type SmartLife
     * @param Array $commands : Liste des commandes à créer
     */
    private function createCommands(SmartLife $smartlife, array $commands)
    {
        $order = 0;
        foreach ($commands as $command) {
            $command['order'] = $order++;
            $smartlife->addCommand($command);
            log::add('SmartLife', 'debug', 'CREATE DEVICE '.$this->device->getId().' : SET command  = '.$command['logicalId']);
        }
    }


    /**
     * Chargement de la configuration d'un équipement depuis le fichier JSON
     * 
     * @return Array
     */
    private function loadJSON()
    {
        $type = $this->device->getType();
        $filecfg = sprintf(__DIR__.self::FILE_CONFIG, $type, $type);
        $content = file_get_contents( $filecfg );
        if ( is_json($content) ) {
            $result = json_decode($content, true);
        } else {
            log::add('SmartLife', 'debug', 'ERROR : Impossible de charger le fichier '.$filecfg);
        }
        return ( isset($result[$type]) ) ? $result[$type] : null;
    }

}
