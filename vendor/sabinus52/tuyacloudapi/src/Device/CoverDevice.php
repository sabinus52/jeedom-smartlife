<?php
/**
 * Classe de l'équipement de type cover (volets roulants)
 *
 * @author Olivier <sabinus52@gmail.com>
 *
 * @package TuyaCloudApi
 */
namespace Sabinus\TuyaCloudApi\Device;

use Sabinus\TuyaCloudApi\Session\Session;
use Sabinus\TuyaCloudApi\Request\Request;


class CoverDevice extends Device implements DeviceInterface
{

    /**
     * Constructeur
     */
    public function __construct(Session $session, $id, $name = '', $icon = '')
    {
        parent::__construct($session, $id, $name, $icon);
        $this->type = DeviceFactory::TUYA_COVER;
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
     * Retourne le support du bouton STOP
     * 
     * @return Boolean
     */
    public function getSupportStop()
    {
        return $this->data['support_stop'];
    }


    /**
     * Affecte le statut de la prise
     * 
     * @param Integer
     */
    public function setState($state)
    {
        $this->data['state'] = $state;
    }


    /**
     * Ouvre le volet
     * 
     * @return Array
     */
    public function open()
    {
        $result = $this->control('turnOnOff', array('value' => 1));
        if ($result == Request::RETURN_SUCCESS) $this->setState(1);
        return $result;
    }


    /**
     * Ferme le volet
     * 
     * @return Array
     */
    public function close()
    {
        $result = $this->control('turnOnOff', array('value' => 0));
        if ($result == Request::RETURN_SUCCESS) $this->setState(2);
        return $result;
    }


    /**
     * Stoppe le volet
     * 
     * @return Array
     */
    public function stop()
    {
        $result = $this->control('startStop', array('value' => 0));
        if ($result == Request::RETURN_SUCCESS) $this->setState(3);
        return $result;
    }
    
}