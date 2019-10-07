<?php
/**
 * Classe de l'équipement inconnu
 *
 * @author Olivier <sabinus52@gmail.com>
 *
 * @package TuyaCloudApi
 */

namespace Sabinus\TuyaCloudApi\Device;

use Sabinus\TuyaCloudApi\TuyaCloudApi;


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
    public function __construct($id, $name = '', $icon = '')
    {
        parent::__construct($id, $name, $icon);
        $this->type = DeviceFactory::UNKNOWN;
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
