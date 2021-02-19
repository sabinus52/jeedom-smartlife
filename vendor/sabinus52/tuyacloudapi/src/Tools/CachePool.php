<?php
/**
 * Librairie du pooler de stockage d'un objet via le système de fichier
 *
 * @author Olivier <sabinus52@gmail.com>
 *
 * @package TuyaCloudApi
 */

namespace Sabinus\TuyaCloudApi\Tools;


class CachePool
{

    /**
     * Chemin contenant le fichier de l'objet
     *
     * @var String
     */
    protected $folder;

    /**
     * Nom du fichier contenant la sauvegarde de l'objet
     * 
     * @var String
     */
    protected $filename;


    /**
     * Constructeur
     * 
     * @param String $folder
     */
    public function __construct($filename, $folder = null)
    {
        $this->filename = $filename;
        $this->folder = (empty($folder)) ? sys_get_temp_dir() : $folder;
    }


    /**
     * Change l'emplacement du dossier
     * 
     * @param String $folder
     */
    public function setFolder($folder)
    {
        $this->folder = $folder;
    }


    /**
     * Change le nom du fichier
     * 
     * @param String $filename
     */
    public function setFileName($filename)
    {
        $this->filename = $filename;
    }


    /**
     * Retourne la valeur de l'objet sauvegardé depuis le système de fichier
     * 
     * @param Integer $ttl : Délai de conservation
     * @return Object|Null si pas de fichier ou delai dépassé
     */
    public function fetchFromCache($ttl = 0)
    {
        $file  = $this->getFilePath();

        // Pas de fichier
        if ( ! @file_exists($file) ) return null;

        // Fichier périmé
        $timestamp = @filemtime($file);
        if ( $timestamp === false ) return null;
        if ( (time() - $timestamp) >= $ttl ) return null;

        // Récupération de l'objet
        $object = @unserialize(@file_get_contents($file));
        if ($object === false) return null;

        return $object;
    }


    /**
     * Enregistre l'objet sur le système de fichier
     * 
     * @param Object $object
     */
    public function storeInCache($object)
    {
        $data = serialize($object);
        $file = $this->getFilePath();

        if (false === @file_put_contents($file, $data)) {
            throw new \Exception(sprintf('Failed to write file "%s".', $file), 0, null, $file);
        }
    }


    /**
     * Efface le fichier
     */
    public function clearFromCache()
    {
        @unlink($this->getFilePath());
    }


    /**
     * Retourne le chemin complet du fichier
     *
     * @return String
     */
    protected function getFilePath()
    {
        return sprintf('%s/%s', $this->folder, $this->filename);
    }

}
