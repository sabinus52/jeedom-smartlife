<?php
/**
 * Fabrique des objets des devices
 *
 * @author Olivier <sabinus52@gmail.com>
 *
 * @package TuyaCloudApi
 */

namespace Sabinus\TuyaCloudApi\Device;

use Sabinus\TuyaCloudApi\Session\Session;


class DeviceFactory
{

    /**
     * Types des différents devices
     */
    const TUYA_UNKNOWN = 'unknown';
    const TUYA_SWITCH  = 'switch';
    const TUYA_LIGHT   = 'light';
    const TUYA_COVER   = 'cover';
    const TUYA_SCENE   = 'scene';
    const TUYA_CLIMATE = 'climate';


    /**
     * Retourne les liste des types disponibles
     * 
     * @return Array
     */
    static public function getTypeAvailable()
    {
        return array(self::TUYA_SCENE, self::TUYA_SWITCH, self::TUYA_COVER, self::TUYA_LIGHT, self::TUYA_CLIMATE);
    }

    
    /**
     * Créer l'objet de l'équipement à partir des données reçus par la découverte des devices
     * 
     * @param Session $session
     * @param Array $datas
     * @return Device
     */
    static public function createDeviceFromDatas(Session $session, array $datas)
    {
        switch ($datas['dev_type']) {
            case self::TUYA_SWITCH :
                $device = new SwitchDevice($session, $datas['id'], $datas['name'], $datas['icon']);
                $device->setData($datas['data']);
                break;
            case self::TUYA_LIGHT :
                $device = new LightDevice($session, $datas['id'], $datas['name'], $datas['icon']);
                $device->setData($datas['data']);
                break;
            case self::TUYA_COVER :
                $device = new CoverDevice($session, $datas['id'], $datas['name'], $datas['icon']);
                $device->setData($datas['data']);
                break;
            case self::TUYA_SCENE :
                $device = new SceneDevice($session, $datas['id'], $datas['name']);
                $device->setData($datas['data']);
                break;
            case self::TUYA_CLIMATE :
                $device = new ClimateDevice($session, $datas['id'], $datas['name'], $datas['icon']);
                $device->setData($datas['data']);
                break;
            default:
                $device = new UnknownDevice($session, $datas['id'], $datas['name'], $datas['icon']);
                $device->setData($datas['data']);
                $device->setDevType($datas['dev_type']);
                break;
        }
        return $device;
    }


    /**
     * Créer l'objet vierge de l'équipement à partir de son ID et son type
     * 
     * @param Session $session
     * @param String $id   : Identifiant de l'équipement
     * @param String $type : Type de l'équipement
     * @param String $name : Nom de l'équipement
     * @return Device
     */
    static public function createDeviceFromId(Session $session, $id, $type, $name = null)
    {
        switch ($type) {
            case self::TUYA_SWITCH :
                $device = new SwitchDevice($session, $id);
                break;
            case self::TUYA_LIGHT :
                $device = new LightDevice($session, $id);
                break;
            case self::TUYA_COVER :
                $device = new CoverDevice($session, $id);
                break;
            case self::TUYA_SCENE :
                $device = new SceneDevice($session, $id);
                break;
            case self::TUYA_CLIMATE :
                $device = new ClimateDevice($session, $id);
                break;
            default:
                return null;
                break;
        }
        $device->setName($name);
        return $device;
    }

}
