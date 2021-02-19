<?php
/**
 * Classe de la configuration de l'objet dans le fichier JSON
 */

use Sabinus\TuyaCloudApi\Device\Device;


class SmartLifeConfig
{

    /**
     * Masque du chemin complet du fichier JSON
     */
    const FILE_CONFIG = '/../config/%s/%s.json';

    /**
     * Objet trouvé
     * 
     * @var Device
     */
    private $device;

    /**
     * Chemin complet du fichier JSON
     * 
     * @var String
     */
    private $fileConfig;

    /**
     * Liste des commandes récupérées dans le fichier JSON
     * 
     * @var Array
     */
    private $commands;


    /**
     * Constructeur
     * 
     * @param Device $device : Objet trouvé
     */
    public function __construct(Device $device)
    {
        $this->device = $device;
        $this->fileConfig = sprintf(__DIR__.self::FILE_CONFIG, $this->device->getType(), $this->device->getType());
        $this->commands = [];
    }


    /**
     * Retourne la liste des commandes
     */
    public function getCommands()
    {
        if ( empty($this->commands) ) $this->loadJSON();
        return $this->commands;
    }


    /**
     * Chargement de la configuration d'un équipement depuis le fichier JSON
     * 
     * @return Boolean si OK
     */
    public function loadJSON()
    {
        // Chargement du fichier
        $content = file_get_contents( $this->fileConfig );
        if ( ! is_json($content) ) return false;
        $result = json_decode($content, true);

        // Vérification du contenu
        $type = $this->device->getType();
        if ( ! isset($result[$type]['commands']) ) return false;

        // Affectation du contenu
        $this->commands = $result[$type]['commands'];
        return true;
    }

}