<?php
/**
 * Classe de l'équipement d'une scène
 *
 * @author Olivier <sabinus52@gmail.com>
 *
 * @package TuyaCloudApi
 */

namespace Sabinus\TuyaCloudApi\Device;

use Sabinus\TuyaCloudApi\Session\Session;


class SceneDevice extends Device implements DeviceInterface
{

    /**
     * Constructeur
     */
    public function __construct(Session $session, $id, $name = '', $icon = '')
    {
        parent::__construct($session, $id, $name, $icon);
        $this->type = DeviceFactory::TUYA_SCENE;
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
     * @return Array
     */
    public function activate()
    {
        return $this->control('turnOnOff', array('value' => 1));
    }
    

    /**
     * Mise à jour des données de l'équipement
     * 
     * @return Array
     */
    public function update()
    {
        return true;
    }

}