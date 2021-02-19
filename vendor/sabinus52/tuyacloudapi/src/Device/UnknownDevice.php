<?php
/**
 * Classe de l'équipement inconnu
 *
 * @author Olivier <sabinus52@gmail.com>
 *
 * @package TuyaCloudApi
 */

namespace Sabinus\TuyaCloudApi\Device;

use Sabinus\TuyaCloudApi\Session\Session;


class UnknownDevice extends Device implements DeviceInterface
{

    /**
     * Type de l'équipement provisoire
     * 
     * @var String
     */
    protected $devType;


    /**
     * Constructeur
     */
    public function __construct(Session $session, $id, $name = '', $icon = '')
    {
        parent::__construct($session, $id, $name, $icon);
        $this->type = DeviceFactory::TUYA_UNKNOWN;
    }


    /**
     * Affecte le type
     * 
     * @param String $type
     */
    public function setDevType($type)
    {
        $this->devType = $type;
    }

}
