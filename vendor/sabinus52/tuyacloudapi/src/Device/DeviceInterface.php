<?php
/**
 * Interface de l'ojet Device
 *
 * @author Olivier <sabinus52@gmail.com>
 *
 * @package TuyaCloudApi
 */

namespace Sabinus\TuyaCloudApi\Device;


 interface DeviceInterface
 {

    /**
     * Constructeur
     * 
     * @param String $id   : Identifiant du device
     * @param String $name : Nom du device
     * @param String $icon : URL de l'icone du device
     */
    public function __construct($id, $name = '', $icon = '');

 }
