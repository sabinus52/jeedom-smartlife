<?php
/**
 * Classe d'évènement sur les devices
 *
 * @author Olivier <sabinus52@gmail.com>
 *
 * @package TuyaCloudApi
 */

namespace Sabinus\TuyaCloudApi\Device;


class DeviceEvent
{

    /**
     * Objet du device
     * 
     * @var Device
     */
    private $device;

    /**
     * Action de la requête
     * 
     * @var String
     */
    private $action;

    /**
     * @var Array
     */
    private $payload;


    /**
     * Constructeur
     * 
     * @param Device $device
     * @param String $action
     * @param Array  $payload
     */
    public function __construct(Device $device, $action, array $payload)
    {
        $this->device = $device;
        $this->action = $action;
        $this->payload = $payload;
    }


    /**
     * Retourne la valeur de l'action de la requête
     * 
     * @return String
     */
    public function getAction()
    {
        return $this->action;
    }


    /**
     * Retourne la payload de la requête
     * 
     * @return Array
     */
    public function getPayload()
    {
        $payload = $this->payload;
        $payload['devId'] = $this->device->getId();
        return $payload;
    }

}