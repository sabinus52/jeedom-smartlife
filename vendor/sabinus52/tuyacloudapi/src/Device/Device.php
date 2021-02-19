<?php
/**
 * Classe abstraite de l'objet Device
 *
 * @author Olivier <sabinus52@gmail.com>
 *
 * @package TuyaCloudApi
 */

namespace Sabinus\TuyaCloudApi\Device;

use Sabinus\TuyaCloudApi\Session\Session;
use Sabinus\TuyaCloudApi\Request\QueryRequest;
use Sabinus\TuyaCloudApi\Request\ControlRequest;


abstract class Device
{

    /**
     * Session au Cloud
     * 
     * @var Session
     */
    protected $session;

    /**
     * Identifiant de l'équipement
     * 
     * @var String
     */
    protected $id;
    
    /**
     * Type de l'équipement
     * 
     * @var String
     */
    protected $type;

    /**
     * Nom de l'équipement
     * 
     * @var String
     */
    protected $name;
    
    /**
     * URL de l'icone de l'équipement
     * 
     * @var String
     */
    protected $icon;

    /**
     * Données supplémentaires du device
     * 
     * @var Array
     */
    protected $data;


    /**
     * Constructeur
     * 
     * @param String $session : Session
     * @param String $id      : Identifiant du device
     * @param String $name    : Nom du device
     * @param String $icon    : URL de l'icone du device
     */
    public function __construct(Session $session, $id, $name = '', $icon = '')
    {
        $this->session = $session;
        $this->id = $id;
        if ($name) $this->name = $name;
        if ($icon) $this->icon = $icon;
    }


    /**
     * Affecte le nom de l'objet
     */
    public function setName($name)
    {
        $this->name = $name;
    }


    /**
     * Affecte les données supplémentaires de l'équipement
     * 
     * @param Array $data
     */
    public function setData($data)
    {
        $this->data = $data;
    }


    /**
     * Retourne l'identifiant de l'équipement
     * 
     * @return String
     */
    public function getId()
    {
        return $this->id;
    }

    
    /**
     * Retourne le type de l'équipement
     * 
     * @return String
     */
    public function getType()
    {
        return $this->type;
    }


    /**
     * Retourne le nom de l'équipement
     * 
     * @return String
     */
    public function getName()
    {
        return $this->name;
    }


    /**
     * Retourne l'URL de l'icone de l'équipement
     * 
     * @return String
     */
    public function getIcon()
    {
        return $this->icon;
    }


    /**
     * Retourne si l'équipement est en ligne ou pas
     * 
     * @return Boolean
     */
    public function isOnline()
    {
        return $this->data['online'];
    }


    /**
     * Retourne les données de l'équipement
     * 
     * @return Array
     */
    public function getData()
    {
        return $this->data;
    }


    /**
     * @return String
     */
    public function __toString()
    {
        return $this->getId().' ('.$this->getType().') : '.$this->getName().' '.print_r($this->getData(), true);
    }


    /**
     * Mise à jour des données de l'équipement
     * 
     * @return Integer
     */
    public function update()
    {
        $payload['devId'] = $this->id;
        $query = new QueryRequest($this->session);
        $query->setDeviceID($this->id);
        $result = $query->request('QueryDevice', $payload);

        if ( $query->getDatas() != null ) $this->setData($query->getDatas());
        return $result;
    }


    /**
     * Envoi une requête de controle de l'équipement
     * 
     * @param String $action  : Valeur de l'action à effectuer
     * @param Array  $payload : Données à envoyer
     * @return Integer
     */
    protected function control($action, $payload)
    {
        $payload['devId'] = $this->id;
        $query = new ControlRequest($this->session);
        return $query->request($action, $payload);
    }

}