<?php
/**
 * Classe de l'équipement de type switch (prise connectée)
 *
 * @author Olivier <sabinus52@gmail.com>
 *
 * @package TuyaCloudApi
 */

namespace Sabinus\TuyaCloudApi\Device;

use Sabinus\TuyaCloudApi\TuyaCloudApi;


class SwitchDevice extends Device implements DeviceInterface
{

    /**
     * Constructeur
     */
    public function __construct($id, $name = '', $icon = '')
    {
        parent::__construct($id, $name, $icon);
        $this->type = DeviceFactory::TUYA_SWITCH;
    }


    /**
     * Retourne le statut de la prise
     * 
     * @return Boolean
     */
    public function getState()
    {
        return $this->data['state'];
    }

    
    /**
     * Allume la prise
     * 
     * @return DeviceEvent
     */
    public function getTurnOnEvent()
    {
        return new DeviceEvent($this, 'turnOnOff', array('value' => 1));
    }

    /**
     * Allume la prise
     * 
     * @param TuyaCloudApi $api
     * @return Array
     */
    public function turnOn(TuyaCloudApi $api)
    {
        return $api->controlDevice($this->id, 'turnOnOff', array('value' => 1));
    }


    /**
     * Eteins la prise
     * 
     * @return DeviceEvent
     */
    public function getTurnOffEvent()
    {
        return new DeviceEvent($this, 'turnOnOff', array('value' => 0));
    }

    /**
     * Eteins la prise
     * 
     * @param TuyaCloudApi $api
     * @return Array
     */
    public function turnOff(TuyaCloudApi $api)
    {
        return $api->controlDevice($this->id, 'turnOnOff', array('value' => 0));
    }
    
}