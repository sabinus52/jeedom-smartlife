<?php

class Session
{

    /**
     * @var String
     */
    private $username;

    /**
     * @var String
     */
    private $password;

    /**
     * @var Integer
     */
    private $country;

    /**
     * @var Platform
     */
    private $platform;


    public function __construct($username, $password, $country, $biztype = null)
    {
        $this->username = $username;
        $this->password = $password;
        $this->country = $country;
        $this->platform = new Platform(
        	($biztype) ? $biztype : Platform::SMART_LIFE
        );
    }


    public function getUsername()
    {
        return $this->username;
    }


    public function getPassword()
    {
        return $this->password;
    }


    public function getCountry()
    {
        return $this->country;
    }


    public function getPlatform()
    {
        return $this->platform;
    }


    public function getBaseUrl($uri)
    {
        return $this->platform->getBaseUrl($uri);
    }

}