<?php
/**
 * Classe de l'équipement de type cover (volets roulants)
 *
 * @author Olivier <sabinus52@gmail.com>
 *
 * @package TuyaCloudApi
 */
namespace Sabinus\TuyaCloudApi\Device;

use Sabinus\TuyaCloudApi\TuyaCloudApi;


class CoverDevice extends Device implements DeviceInterface
{

    /**
     * Constructeur
     */
    public function __construct($id, $name = '', $icon = '')
    {
        parent::__construct($id, $name, $icon);
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
     * Ouvre le volet
     * 
     * @return DeviceEvent
     */
    public function getOpenEvent()
    {
        return new DeviceEvent($this, 'turnOnOff', array('value' => 1));
    }

    /**
     * Ouvre le volet
     * 
     * @param TuyaCloudApi $api
     * @return Array
     */
    public function open(TuyaCloudApi $api)
    {
        return $api->controlDevice($this->id, 'turnOnOff', array('value' => 1));
    }


    /**
     * Ferme le volet
     * 
     * @return DeviceEvent
     */
    public function getCloseEvent()
    {
        return new DeviceEvent($this, 'turnOnOff', array('value' => 0));
    }

    /**
     * Ferme le volet
     * 
     * @param TuyaCloudApi $api
     * @return Array
     */
    public function close(TuyaCloudApi $api)
    {
        return $api->controlDevice($this->id, 'turnOnOff', array('value' => 0));
    }


    /**
     * Stoppe le volet
     * 
     * @return DeviceEvent
     */
    public function getStopEvent()
    {
        return new DeviceEvent($this, 'startStop', array('value' => 0));
    }

    /**
     * Stoppe le volet
     * 
     * @param TuyaCloudApi $api
     * @return Array
     */
    public function stop(TuyaCloudApi $api)
    {
        return $api->controlDevice($this->id, 'startStop', array('value' => 0));
    }
    
}