<?php
/**
 * Classe de l'équipement de type switch (prise connectée)
 *
 * @author Olivier <sabinus52@gmail.com>
 *
 * @package TuyaCloudApi
 */

namespace Sabinus\TuyaCloudApi\Device;

use Sabinus\TuyaCloudApi\Session\Session;
use Sabinus\TuyaCloudApi\Request\Request;


class SwitchDevice extends Device implements DeviceInterface
{

    /**
     * Constructeur
     */
    public function __construct(Session $session, $id, $name = '', $icon = '')
    {
        parent::__construct($session, $id, $name, $icon);
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
     * Affecte le statut de la prise
     * 
     * @param Boolean
     */
    public function setState($state)
    {
        $this->data['state'] = $state;
    }

    
    /**
     * Allume la prise
     * 
     * @return Array
     */
    public function turnOn()
    {
        $result = $this->control('turnOnOff', array('value' => 1));
        if ($result == Request::RETURN_SUCCESS) $this->setState(true);
        return $result;
    }


    /**
     * Eteins la prise
     * 
     * @return Array
     */
    public function turnOff()
    {
        $result = $this->control('turnOnOff', array('value' => 0));
        if ($result == Request::RETURN_SUCCESS) $this->setState(false);
        return $result;
    }
    
}