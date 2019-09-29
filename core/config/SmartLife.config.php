<?php
/**
 * Classe de configuration du plugin
 *  - Infos des objets
 *  - Action sur les objets
 */

require_once __DIR__ . '/../../vendor/autoload.php';
use Sabinus\TuyaCloudApi\Device\DeviceFactory;


class SmartLifeConfig
{

    /**
     * Constantes des infos
     */
    const STATE         = 'STATE';
    const BRIGHTNESS    = 'BRIGHTNESS';
    const TEMPERATURE   = 'TEMPERATURE';
    const COLORHUE      = 'COLORHUE';
    const SATURATION    = 'SATURATION';


    /**
     * Constantes des actions
     */
    const REFRESH           = 'REFRESH';
    const TURN_ON           = 'TurnOn';
    const TURN_OFF          = 'TurnOff';
    const ACTIVATE          = 'Activate';
    const SET_BRIGHTNESS    = 'SetBrightness';
    const SET_COLOR         = 'SetColor';
    const SET_TEMPERATURE   = 'SetTemperature';
    const OPEN              = 'Open';
    const CLOSE             = 'Close';
    const STOP              = 'Stop';


    /**
     * Liste des infos possibles
     */
    static private $infos = array(
        self::STATE => array(
            'name' => 'Status',
            'logicalId' => self::STATE,
            'type' => 'info',
            'subType' => 'binary',
            'order' => 10,
            'isVisible' => true,
        ),
        self::BRIGHTNESS => array(
            'name' => 'Luminosité-Info',
            'logicalId' => self::BRIGHTNESS,
            'type' => 'info',
            'subType' => 'numeric',
            'order' => 20,
            'isVisible' => true,
        ),
        self::TEMPERATURE => array(
            'name' => 'Blanc-Info',
            'logicalId' => self::TEMPERATURE,
            'type' => 'info',
            'subType' => 'numeric',
            'order' => 21,
            'isVisible' => true,
        ),
        self::COLORHUE => array(
            'name' => 'Couleur-Info',
            'logicalId' => self::COLORHUE,
            'type' => 'info',
            'subType' => 'string',
            'order' => 22,
            'isVisible' => true,
        ),
        self::SATURATION => array(
            'name' => 'Saturation-Info',
            'logicalId' => self::SATURATION,
            'type' => 'info',
            'subType' => 'numeric',
            'order' => 23,
            'isVisible' => true,
        ),
    );


    /**
     * Liste des actions possibles
     */
    static private $actions = array(
        self::REFRESH => array(
            'name' => 'Refresh',
            'logicalId' => self::REFRESH,
            'type' => 'action',
            'subType' => 'other',
            'order' => 50,
            'isVisible' => true,
            'forceReturnLineAfter' => '0',
        ),
        self::TURN_ON => array(
            'name' => 'Allumer',
            'logicalId' => self::TURN_ON,
            'type' => 'action',
            'subType' => 'other',
            'order' => 51,
            'isVisible' => true,
            'forceReturnLineAfter' => '0',
        ),
        self::TURN_OFF => array(
            'name' => 'Eteindre',
            'logicalId' => self::TURN_OFF,
            'type' => 'action',
            'subType' => 'other',
            'order' => 52,
            'isVisible' => true,
            'forceReturnLineAfter' => '0',
        ),
        self::ACTIVATE => array(
            'name' => 'Activer',
            'logicalId' => self::ACTIVATE,
            'type' => 'action',
            'subType' => 'other',
            'order' => 53,
            'isVisible' => true,
            'forceReturnLineAfter' => '0',
        ),
        self::SET_BRIGHTNESS => array(
            'name' => 'Luminosité',
            'logicalId' => self::SET_BRIGHTNESS,
            'type' => 'action',
            'subType' => 'slider',
            'value' => self::BRIGHTNESS,
            'order' => 54,
            'isVisible' => true,
            'forceReturnLineAfter' => '0',
        ),
        self::SET_COLOR => array(
            'name' => 'Couleur',
            'logicalId' => self::SET_COLOR,
            'type' => 'action',
            'subType' => 'color',
            'value' => self::COLORHUE,
            'order' => 55,
            'isVisible' => true,
            'forceReturnLineAfter' => '0',
        ),
        self::SET_TEMPERATURE => array(
            'name' => 'Blanc',
            'logicalId' => self::SET_TEMPERATURE,
            'type' => 'action',
            'subType' => 'slider',
            'value' => self::TEMPERATURE,
            'order' => 56,
            'isVisible' => true,
            'forceReturnLineAfter' => '0',
        ),
        self::OPEN => array(
            'name' => 'Ouvrir',
            'logicalId' => self::OPEN,
            'type' => 'action',
            'subType' => 'other',
            'order' => 57,
            'isVisible' => true,
            'forceReturnLineAfter' => '0',
        ),
        self::CLOSE => array(
            'name' => 'Fermer',
            'logicalId' => self::CLOSE,
            'type' => 'action',
            'subType' => 'other',
            'order' => 58,
            'isVisible' => true,
            'forceReturnLineAfter' => '0',
        ),
        self::STOP => array(
            'name' => 'Stopper',
            'logicalId' => self::STOP,
            'type' => 'action',
            'subType' => 'other',
            'order' => 59,
            'isVisible' => true,
            'forceReturnLineAfter' => '0',
        ),
    );


    /**
     * Retourne la configuration d'un équipement en fonction de son type
     * 
     * @param String $type
     * @return Array
     */
    static public function getConfig($type)
    {
        switch ($type) {
            case DeviceFactory::COVER   : return self::getConfigCover(); break;
            case DeviceFactory::SWITCH  : return self::getConfigSwitch(); break;
            case DeviceFactory::LIGHT   : return self::getConfigLight(); break;
            case DeviceFactory::SCENE   : return self::getConfigScene(); break;
            case DeviceFactory::UNKNOWN : return array(); break;
            default : return null;
        }
    }


    /**
     * Retourne les infos d'un équipement en fonction de son type
     * 
     * @param String $type
     * @return Array
     */
    static public function getConfigInfos($type)
    {
        $configs = self::getConfig($type);
        if ($configs == null) return null;
        $result = array();
        foreach ($configs as $config) {
            if ($config['type'] == 'info') $result[] = $config;
        }
        return $result;
    }


    /**
     * Retourne la configuration d'un volet roulant
     * 
     * @return Array
     */
    static private function getConfigCover()
    {
        return array(
            self::$infos[self::STATE],
            self::$actions[self::REFRESH],
            self::$actions[self::OPEN],
            self::$actions[self::CLOSE],
            self::$actions[self::STOP],
        );
    }

    /**
     * Retourne la configuration d'une prise connectée
     * 
     * @return Array
     */
    static private function getConfigSwitch()
    {
        return array(
            self::$infos[self::STATE],
            self::$actions[self::REFRESH],
            self::$actions[self::TURN_ON],
            self::$actions[self::TURN_OFF],
        );
    }

    /**
     * Retourne la configuration d'une scène
     * 
     * @return Array
     */
    static private function getConfigScene()
    {
        return array(
            self::$actions[self::ACTIVATE],
        );
    }

    /**
     * Retourne la configuration d'une ampoule
     * 
     * @return Array
     */
    static private function getConfigLight()
    {
        return array(
            self::$infos[self::STATE],
            self::$infos[self::BRIGHTNESS],
            self::$infos[self::TEMPERATURE],
            self::$infos[self::COLORHUE],
            self::$infos[self::SATURATION],
            self::$actions[self::REFRESH],
            self::$actions[self::TURN_ON],
            self::$actions[self::TURN_OFF],
            self::$actions[self::SET_BRIGHTNESS],
            self::$actions[self::SET_COLOR],
            self::$actions[self::SET_TEMPERATURE],
        );
    }

}