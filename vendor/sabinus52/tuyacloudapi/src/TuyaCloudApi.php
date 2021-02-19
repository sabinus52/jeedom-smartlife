<?php
/**
 * Librairie de base de l'API
 *
 * @author Olivier <sabinus52@gmail.com>
 *
 * @package TuyaCloudApi
 */

namespace Sabinus\TuyaCloudApi;

use Sabinus\TuyaCloudApi\Session\Session;
use Sabinus\TuyaCloudApi\Device\Device;
use Sabinus\TuyaCloudApi\Request\DiscoveryRequest;


class TuyaCloudApi
{

    /**
     * @var Session
     */
    private $session;
    
    /**
     * Tableau des devives trouvés
     */
    private $devices;
    


    /**
     * Contructeur
     * 
     * @param Session $session
     */
    public function __construct(Session $session)
    {
        $this->session = $session;
        $this->devices = [];
    }


    /**
     * Retourne la session
     * 
     * @return Session
     */
    public function getSession()
    {
        return $this->session;
    }


    /**
     * Vérifie la connexion
     * 
     * @return Boolean
     */
    public function checkConnection()
    {
        $token = $this->session->getToken();
        return ( $token ) ? true : false;
    }


    /**
     * Recherche tous les équipements disponibles pour cette session
     * 
     * @return Boolean
     */
    public function discoverDevices()
    {
        $reqDiscovery = new DiscoveryRequest($this->session);
        $result = $reqDiscovery->request();
        $this->devices = $reqDiscovery->fetchDevices();
        return $result;
    }


    /**
     * Retourne la liste des objets trouvés
     * 
     * @return Array of Device
     */
    public function getAllDevices()
    {
        if ( empty($this->devices) ) $this->discoverDevices();
        return $this->devices;
    }


    /**
     * Retourne l'objet du device
     * 
     * @param String $id : Identifiant du device
     * @return Device
     */
    public function getDeviceById($id)
    {
        if ( empty($this->devices) ) $this->discoverDevices();
        foreach ($this->devices as $device) {
            if ($device && $device->getId() == $id)
                return $device;
        }
        return null;
    }

}