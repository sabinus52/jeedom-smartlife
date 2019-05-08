<?php

class SwitchDevice
{
    
    protected $id;

    protected $name;

    protected $state;

    protected $online;


    public function __construct($id, $name)
    {
        $this->id = $id;
        $this->name = $name;
    }


    public function getId()
    {
        return $this->id;
    }


    public function getName()
    {
        return $this->name;
    }


    public function getState()
    {
        return $this->state;
    }

    public function setState($state)
    {
        $this->state = $state;
    }


    public function getOnline()
    {
        return $this->online;
    }

    public function setOnline($online)
    {
        $this->online = $online;
    }


    public function getOnEvent()
    {
        return new DeviceEvent($this, 'turnOnOff', array('value' => 1));
    }

    public function getOffEvent()
    {
        return new DeviceEvent($this, 'turnOnOff', array('value' => 0));
    }

}