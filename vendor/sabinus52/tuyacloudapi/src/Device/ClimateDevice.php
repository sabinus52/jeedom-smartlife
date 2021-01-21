<?php
/**
 * Classe de l'équipement de type climate (climatiseur)
 *
 * @author Olivier <sabinus52@gmail.com>
 *
 * @package TuyaCloudApi
 */

namespace Sabinus\TuyaCloudApi\Device;

use Sabinus\TuyaCloudApi\TuyaCloudApi;


class ClimateDevice extends Device implements DeviceInterface
{

    /**
     * Vitesse du climatiseur
     */
    const LOW    = 1;
    const MEDIUM = 2;
    const HIGH   = 3;

    /**
     * Unités des températures
     */
    const CELSIUS = 'celsius';
    const FAHRENHEIT = 'fahrenheit';


    /**
     * Constructeur
     */
    public function __construct($id, $name = '', $icon = '')
    {
        parent::__construct($id, $name, $icon);
        $this->type = DeviceFactory::TUYA_CLIMATE;
    }


    /**
     * Retourne le statut du climatiseur (Allumé ou éteinte)
     * 
     * @return Boolean
     */
    public function getState()
    {
        return $this->data['state'];
    }


    /**
     * Retourne la température programmée du climatiseur en Celsius
     * 
     * @return Integer
     */
    public function getThermostat()
    {
        return $this->parseTemperature($this->data['temperature']);
    }


    /**
     * Retourne la température courante du climatiseur en Celsius
     * 
     * @return Integer
     */
    public function getTemperature()
    {
        if ( ! isset($this->data['current_temperature']) ) return null;
        return $this->parseTemperature($this->data['current_temperature']);
    }


    /**
     * Retourne la vitesse du climatiseur
     * 
     * @return Integer
     */
    public function getWindSpeed()
    {
        if ( ! $this->getSupportSpeedWind() )
            return null;
        else
            return $this->data['windspeed'];
    }


    /**
     * Retourne le mode du climatiseur
     * 
     * @return Integer
     */
    public function getMode()
    {
        if ( ! $this->getSupportMode() )
            return null;
        else
            return $this->data['mode'];
    }


    /**
     * Retourne la température minimum du climatiseur
     * 
     * @return Integer
     */
    public function getMinTemperature()
    {
        return $this->parseTemperature($this->data['min_temper']);
    }


    /**
     * Retourne la température maximum du climatiseur
     * 
     * @return Integer
     */
    public function getMaxTemperature()
    {
        return $this->parseTemperature($this->data['max_temper']);
    }


    /**
     * Retourne l'unité de la température du climatiseur
     * 
     * @return Integer
     */
    public function getUnitTemperature()
    {
        if ( ! isset($this->data['temp_unit'] ) ) return self::CELSIUS;
        return $this->data['temp_unit'];
    }


    /**
     * Si unité en Fahrenheit
     * 
     * @return Boolean
     */
    public function isUnitFahrenheit()
    {
        return ( strtolower($this->getUnitTemperature()) == self::FAHRENHEIT );
    }


    /**
     * Retourne si le climatiseur supporte la vitesse
     * 
     * @return Boolean
     */
    public function getSupportSpeedWind()
    {
        return (isset($this->data['windspeed'])) ? true : false;
    }


    /**
     * Retourne si le climatiseur supporte les différents modes
     * 
     * @return Boolean
     */
    public function getSupportMode()
    {
        return (isset($this->data['support_mode'])) ? true : false;
    }


    /**
     * Retourne les différents modes supportées
     * 
     * @return Boolean
     */
    public function getListMode()
    {
        return (isset($this->data['support_mode'])) ? $this->data['support_mode'] : null;
    }


    /**
     * Allume le climatiseur
     * 
     * @return DeviceEvent
     */
    public function getTurnOnEvent()
    {
        return new DeviceEvent($this, 'turnOnOff', array('value' => 1));
    }

    /**
     * Allume le climatiseur
     * 
     * @param TuyaCloudApi $api
     * @return Array
     */
    public function turnOn(TuyaCloudApi $api)
    {
        return $api->controlDevice($this->id, 'turnOnOff', array('value' => 1));
    }


    /**
     * Eteins le climatiseur
     * 
     * @return DeviceEvent
     */
    public function getTurnOffEvent()
    {
        return new DeviceEvent($this, 'turnOnOff', array('value' => 0));
    }

    /**
     * Eteins le climatiseur
     * 
     * @param TuyaCloudApi $api
     * @return Array
     */
    public function turnOff(TuyaCloudApi $api)
    {
        return $api->controlDevice($this->id, 'turnOnOff', array('value' => 0));
    }


    /**
     * Affecte la température du climatiseur
     * 
     * @param Integer $value : Valeur de la température
     * @return DeviceEvent
     */
    public function getSetThermostatEvent($value)
    {
        return new DeviceEvent($this, 'temperatureSet', array('value' => $this->generateThermostat($value)));
    }

    /**
     * Affecte la température du climatiseur
     * 
     * @param TuyaCloudApi $api
     * @param Integer $value : Valeur de la température
     * @return Array
     */
    public function setThermostat(TuyaCloudApi $api, $value)
    {
        return $api->controlDevice($this->id, 'temperatureSet', array('value' => $this->generateThermostat($value)));
    }


    /**
     * Affecte la vitesse du climatiseur
     * 
     * @param Integer $value : Valeur de la vitesse
     * @return DeviceEvent
     */
    public function getSetSpeedWindEvent($value)
    {
        return new DeviceEvent($this, 'windSpeedSet', array('value' => $value));
    }

    /**
     * Affecte la vitesse du climatiseur
     * 
     * @param TuyaCloudApi $api
     * @param Integer $value : Valeur de la vitesse
     * @return Array
     */
    public function setSpeedWind(TuyaCloudApi $api, $value)
    {
        return $api->controlDevice($this->id, 'windSpeedSet', array('value' => $value));
    }


    /**
     * Affecte le mode du climatiseur
     * 
     * @param Integer $value : Valeur du mode
     * @return DeviceEvent
     */
    public function getSetModeEvent($value)
    {
        return new DeviceEvent($this, 'modeSet', array('value' => $value));
    }

    /**
     * Affecte le mode du climatiseur
     * 
     * @param TuyaCloudApi $api
     * @param Integer $value : Valeur du mode
     * @return Array
     */
    public function setMode(TuyaCloudApi $api, $value)
    {
        return $api->controlDevice($this->id, 'modeSet', array('value' => $value));
    }


    /**
     * Parse le retour de la température en fonction de son unité et de sa valeur
     * 
     * @param Float $value : Température
     * @return Float
     */
    private function parseTemperature($value)
    {
        if ( $this->isUnitFahrenheit() )
            return $this->convertFahrenheitToCelsius($value);
        elseif ( $value >= 65 )
            return round($value / 10, 1);
        else
            return $value;
    }


    /**
     * Génération de la température duu Thermostat en fonction de son unité
     */
    private function generateThermostat($value)
    {
        if ( $this->isUnitFahrenheit() ) 
            return $this->convertCelsiusToFahrenheit($value);
        elseif ( $this->data['temperature'] >= 65 )
            return $value * 10;
        else
            return $value;
    }


    /**
     * Retourne la température en Celsius
     * 
     * @param Integer $temp : Température en Fahrenheit
     * @return Float
     */
    private function convertFahrenheitToCelsius($temp)
    {
        return round( ($temp - 32) * 5 / 9, 1);
    }


    /**
     * Retourne la température en Fahrenheit
     * 
     * @param Float $temp : Température en Celsius
     * @return Integer
     */
    private function convertCelsiusToFahrenheit($temp)
    {
        return round( ($temp * 9 / 5) + 32, 0);
    }
    
}
