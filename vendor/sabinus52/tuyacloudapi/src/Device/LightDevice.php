<?php
/**
 * Classe de l'équipement de type light (lampe connectée)
 *
 * @author Olivier <sabinus52@gmail.com>
 *
 * @package TuyaCloudApi
 */

namespace Sabinus\TuyaCloudApi\Device;

use Sabinus\TuyaCloudApi\TuyaCloudApi;


class LightDevice extends Device implements DeviceInterface
{

    /**
     * Constructeur
     */
    public function __construct($id, $name = '', $icon = '')
    {
        parent::__construct($id, $name, $icon);
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
     * Retourne la luminosité de la lampe en pourcentage (%)
     * 
     * @return Integer
     */
    public function getBrightness()
    {
        if ($this->data['color_mode'] == 'colour')
            return $this->data['color']['brightness'];
        else
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
        elseif ( $this->data['color_mode'] == 'colour' )
            return $this->data['color']['hue'];
        else
            return 0;
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
        elseif ( $this->data['color_mode'] == 'colour' )
            return $this->data['color']['saturation'];
        else
            return 0;
    }
    public function getSaturation() { return $this->getColorSaturation(); }


    /**
     * Retourne si la lampe supporte la couleur
     * 
     * @return Boolean
     */
    public function getSupportColor()
    {
        return (isset($this->data['color'])) ? true : false;
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
     * @return DeviceEvent
     */
    public function getTurnOnEvent()
    {
        return new DeviceEvent($this, 'turnOnOff', array('value' => 1));
    }

    /**
     * Allume la lampe
     * 
     * @param TuyaCloudApi $api
     * @return Array
     */
    public function turnOn(TuyaCloudApi $api)
    {
        return $api->controlDevice($this->id, 'turnOnOff', array('value' => 1));
    }


    /**
     * Eteins la lampe
     * 
     * @return DeviceEvent
     */
    public function getTurnOffEvent()
    {
        return new DeviceEvent($this, 'turnOnOff', array('value' => 0));
    }

    /**
     * Eteins la lampe
     * 
     * @param TuyaCloudApi $api
     * @return Array
     */
    public function turnOff(TuyaCloudApi $api)
    {
        return $api->controlDevice($this->id, 'turnOnOff', array('value' => 0));
    }

    
    /**
     * Affecte la luminosité
     * 
     * @param Integer $value : Valeur de la luminosité en pourcentage (%)
     * @return DeviceEvent
     */
    public function getSetBrightnessEvent($value)
    {
        return new DeviceEvent($this, 'brightnessSet', array('value' => $value));
    }

    /**
     * Affecte la luminosité
     * 
     * @param TuyaCloudApi $api
     * @param Integer $value : Valeur de la luminosité en pourcentage (%)
     * @return Array
     */
    public function setBrightness(TuyaCloudApi $api, $value)
    {
        return $api->controlDevice($this->id, 'brightnessSet', array('value' => $value));
    }


    /**
     * Affecte une couleur
     * 
     * @param Integer $color
     * @param Integer $saturation : Saturation en pourcentage (%)
     * @param Integer $brightness : Luminosité en pourcentage (%)
     * @return DeviceEvent
     */
    public function getSetColorEvent($color, $saturation, $brightness = 0)
    {
        $hsv = array();
        $hsv['hue'] = $color;
        $hsv['saturation'] = round($saturation * 255 / 100); // 0-255
        $hsv['brightness'] = $brightness; // N'a pas l'air de fonctionner mais obligatoire
        if ($saturation == 0) $hsv['hue'] = 0;
        return new DeviceEvent($this, 'colorSet', array('color' => $hsv));
    }

    /**
     * Affecte une couleur
     * 
     * @param TuyaCloudApi $api
     * @param Integer $color
     * @param Integer $saturation : Saturation en pourcentage (%)
     * @return Array
     */
    public function setColor(TuyaCloudApi $api, $color, $saturation, $brightness = 0)
    {
        $hsv = array();
        $hsv['hue'] = $color;
        $hsv['saturation'] = round($saturation * 255 / 100); // 0-255
        $hsv['brightness'] = $brightness; // N'a pas l'air de fonctionner mais obligatoire
        if ($saturation == 0) $hsv['hue'] = 0;
        return $api->controlDevice($this->id, 'colorSet', array('color' => $hsv));
    }


    /**
     * Affecte la température de la lumière
     * 
     * @param Integer $value : Valeur de la température
     * @return DeviceEvent
     */
    public function getSetTemperatureEvent($value)
    {
        return new DeviceEvent($this, 'colorTemperatureSet', array('value' => $value));
    }

    /**
     * Affecte la température de la lumière
     * 
     * @param TuyaCloudApi $api
     * @param Integer $value : Valeur de la température
     * @return Array
     */
    public function setTemperature(TuyaCloudApi $api, $value)
    {
        return $api->controlDevice($this->id, 'colorTemperatureSet', array('value' => $value));
    }
    
}