<?php
/**
 * Classe de l'équipement d'une scène
 *
 * @author Olivier <sabinus52@gmail.com>
 *
 * @package TuyaCloudApi
 */

namespace Sabinus\TuyaCloudApi\Device;

use Sabinus\TuyaCloudApi\TuyaCloudApi;


class SceneDevice extends Device implements DeviceInterface
{

    /**
     * Constructeur
     */
    public function __construct($id, $name = '', $icon = '')
    {
        parent::__construct($id, $name, $icon);
        $this->type = DeviceFactory::SCENE;
    }


    /**
     * Retourne si l'équipement est en ligne ou pas
     * 
     * @return Boolean
     */
    public function isOnline()
    {
        return true;
    }

  
    /**
     * Active la scène
     * 
     * @return DeviceEvent
     */
    public function getActivateEvent()
    {
        return new DeviceEvent($this, 'turnOnOff', array('value' => 1));
    }

    /**
     * Active la scène
     * 
     * @param TuyaCloudApi $api
     * @return Array
     */
    public function activate(TuyaCloudApi $api)
    {
        return $api->controlDevice($this->id, 'turnOnOff', array('value' => 1));
    }
    

    /**
     * Mise à jour des données de l'équipement
     * 
     * @param TuyaCloudApi $api
     * @return Array
     */
    public function update(TuyaCloudApi $api)
    {
        return true;
    }

}