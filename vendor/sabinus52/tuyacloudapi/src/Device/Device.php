<?php
/**
 * Classe abstraite de l'objet Device
 *
 * @author Olivier <sabinus52@gmail.com>
 *
 * @package TuyaCloudApi
 */

namespace Sabinus\TuyaCloudApi\Device;

use Sabinus\TuyaCloudApi\TuyaCloudApi;


abstract class Device
{
   
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
     * @param String $id   : Identifiant du device
     * @param String $name : Nom du device
     * @param String $icon : URL de l'icone du device
     */
    public function __construct($id, $name = '', $icon = '')
    {
        $this->id = $id;
        if ($name) $this->name = $name;
        if ($icon) $this->icon = $icon;
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
     * Mise à jour des données de l'équipement
     * 
     * @param TuyaCloudApi $api
     * @return Array
     */
    public function update(TuyaCloudApi $api)
    {
        sleep(1);
        $response = $api->controlDevice($this->id, 'QueryDevice', array(), 'query');
        $this->setData($response['payload']['data']);
        return true;
    }

}