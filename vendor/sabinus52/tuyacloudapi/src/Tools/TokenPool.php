<?php
/**
 * Librairie du pooler de stockage du token Tuya via le système de fichier
 *
 * @author Olivier <sabinus52@gmail.com>
 *
 * @package TuyaCloudApi
 */

namespace Sabinus\TuyaCloudApi\Tools;

use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Exception\FileNotFoundException;
use Symfony\Component\Filesystem\Exception\IOException;


class TokenPool
{

    const TOKEN_FILE = 'tuya.token';


    /**
     * @var Filesystem
     */
    private $filesystem;

    /**
     * Chemin contenant le fichier du token
     *
     * @var String
     */
    private $folder;


    /**
     * Constructeur
     * 
     * @param String $folder
     */
    public function __construct($folder = null)
    {
        $this->folder = (empty($folder)) ? sys_get_temp_dir() : $folder;

        $this->filesystem = new FileSystem();
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
     * Retourne la valeur du token sauvegardé depuis le système de fichier
     * 
     * @return Token
     */
    public function fetchTokenFromCache()
    {
        $file  = $this->getFilePath();

        try {
            $token = @unserialize(@file_get_contents($file));
            if ($token === false) {
                return null;
            }
        } catch (FileNotFoundException $e) {
            return null;
        }

        return $token;
    }


    /**
     * Enregistre le token sur le système de fichier
     * 
     * @param Token $token
     */
    public function storeTokenInCache($token)
    {
        $data = serialize($token);
        $file = $this->getFilePath();

        if (false === @file_put_contents($file, $data)) {
            throw new IOException(sprintf('Failed to write file "%s".', $file), 0, null, $file);
        }
    }


    /**
     * Efface le fichier du token
     */
    public function clearFromCache()
    {
        try {
            $this->filesystem->remove($this->getFilePath());
        } catch (FileNotFoundException $e) {
            return true;
        }
    }


    /**
     * Retourne le chemin complet du fichier
     *
     * @return String
     */
    private function getFilePath()
    {
        return sprintf('%s/%s', $this->folder, self::TOKEN_FILE);
    }

}
