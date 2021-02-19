<?php
/**
 * Classe de l'équipement de type light (lampe connectée)
 *
 * @author Olivier <sabinus52@gmail.com>
 *
 * @package TuyaCloudApi
 */

namespace Sabinus\TuyaCloudApi\Device;

use Sabinus\TuyaCloudApi\Session\Session;
use Sabinus\TuyaCloudApi\Request\Request;


class LightDevice extends Device implements DeviceInterface
{

    /**
     * Constructeur
     */
    public function __construct(Session $session, $id, $name = '', $icon = '')
    {
        parent::__construct($session, $id, $name, $icon);
        $this->type = DeviceFactory::TUYA_LIGHT;
    }


    /**
     * Retourne le statut de la lampe (Allumé ou éteinte)
     * 
     * @return Boolean
     */
    public function getState()
    {
        return $this->data['state'];
    }

    /**
     * Affecte le statut de la lamps
     * 
     * @param Boolean
     */
    public function setState($state)
    {
        $this->data['state'] = $state;
    }


    /**
     * Retourne la luminosité de la lampe en pourcentage (%)
     * 
     * @return Integer
     */
    public function getBrightness()
    {
        return round($this->data['brightness'] / 255 * 100);
    }


    /**
     * Retourne la température de la lampe
     * 
     * @return Integer
     */
    public function getTemperature()
    {
        if ( !$this->getSupportTemperature() )
            return null;
        else
            return $this->data['color_temp'];
    }


    /**
     * Retourne la couleur de la lampe
     * 
     * @return Integer
     */
    public function getColorHue()
    {
        if ( !$this->getSupportColor() )
            return null;
        elseif ( isset($this->data['color']['hue']) )
            return $this->data['color']['hue'];
        else
            return null;
    }


    /**
     * Retourne la saturation de la couleur de la lampe en pourcentage (%)
     * 
     * @return Integer
     */
    public function getColorSaturation()
    {
        if ( !$this->getSupportColor() )
            return null;
        elseif ( isset($this->data['color']['saturation']) )
            return $this->data['color']['saturation'];
        else
            return null;
    }
    public function getSaturation() { return $this->getColorSaturation(); }


    /**
     * Retourne si la lampe supporte la couleur
     * 
     * @return Boolean
     */
    public function getSupportColor()
    {
        return ($this->data['color_mode'] == 'colour') ? true : false;
    }


    /**
     * Retourne si la lampe supporte la température de la lumière
     * 
     * @return Boolean
     */
    public function getSupportTemperature()
    {
        return (isset($this->data['color_temp'])) ? true : false;
    }


    /**
     * Allume la lampe
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
     * Eteins la lampe
     * 
     * @return Array
     */
    public function turnOff()
    {
        $result = $this->control('turnOnOff', array('value' => 0));
        if ($result == Request::RETURN_SUCCESS) $this->setState(false);
        return $result;
    }

    
    /**
     * Affecte la luminosité
     * 
     * @param Integer $value : Valeur de la luminosité en pourcentage (%)
     * @return Array
     */
    public function setBrightness($value)
    {
        $result = $this->control('brightnessSet', array('value' => $value));
        if ($result == Request::RETURN_SUCCESS) $this->data['brightness'] = round($value * 2.55);
        return $result;
    }


    /**
     * Affecte une couleur
     * 
     * @param Integer $color
     * @param Integer $saturation : Saturation en pourcentage (%)
     * @return Array
     */
    public function setColor($color, $saturation, $brightness = 0)
    {
        $hsv = array();
        $hsv['hue'] = $color;
        $hsv['saturation'] = round($saturation * 255 / 100); // 0-255
        $hsv['brightness'] = $brightness; // N'a pas l'air de fonctionner mais obligatoire
        if ($saturation == 0) $hsv['hue'] = 0;
        return $this->control('colorSet', array('color' => $hsv));
    }


    /**
     * Affecte la température de la lumière
     * 
     * @param Integer $value : Valeur de la température
     * @return Array
     */
    public function setTemperature($value)
    {
        $result = $this->control('colorTemperatureSet', array('value' => $value));
        if ($result == Request::RETURN_SUCCESS) $this->data['color_temp'] = $value;
        return $result;
    }
    
}