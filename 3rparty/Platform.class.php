<?php

class Platform
{

	const BASE_URL_FORMAT = 'https://px1.tuya%s.com';

	/**
	 * Constante de la liste des plateformes Tuya ou Smart Life
	 */
	const TUYA = 'tuya';
    const SMART_LIFE = 'smart_life';

    
    /**
     * Liste des regions des plateformes
     */
    const CN = 'cn';
    const EU = 'eu';
    const US = 'us';


    private $biztype;


    private $region;

    

    public function __construct($biztype, $region = self::EU)
    {
        $this->biztype = $biztype;
        $this->region = $region;
    }


    public function getBizType()
    {
    	return $this->biztype;
    }


    public function getRegion()
    {
        return $this->region;
    }


    public function setRegionFromToken($token)
    {
        $prefix = substr($token, 0, 2);
        $this->region = '';
        switch ($prefix) {
            case 'AY' : $this->region = self::CN; break;
            case 'EU' : $this->region = self::EU; break;
            case 'US' : $this->region = self::US; break;
        }
    }


    public function getBaseUrl($uri)
    {
        return sprintf(self::BASE_URL_FORMAT, $this->region).$uri;
    }


}