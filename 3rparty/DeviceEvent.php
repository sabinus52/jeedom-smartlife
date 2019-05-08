<?php

class DeviceEvent
{

    private $device;

    private $action;

    private $payload;


    public function __construct($device, $action, $payload)
    {
        $this->device = $device;
        $this->action = $action;
        $this->payload = $payload;
    }


    public function getAction()
    {
        return $this->action;
    }

    public function getPayload()
    {
        $payload = $this->payload;
        $payload['devId'] = $this->device->getId();
        return $payload;
    }

}