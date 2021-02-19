<?php
/**
 * Classe de la requête de l'interrogation sur un objet
 *
 * @author Olivier <sabinus52@gmail.com>
 *
 * @package TuyaCloudApi
 */

namespace Sabinus\TuyaCloudApi\Request;

use Sabinus\TuyaCloudApi\Session\Session;
use Sabinus\TuyaCloudApi\Tools\CachePool;


class QueryRequest extends Request implements RequestInterface
{    

    /**
     * NameSpace de le requête
     */
    const NAMESPACE = 'query';

    /**
     * Délai entre 2 requêtes de query (restriction Tuya)
     */
    const CACHE_DELAY = 120;

    /**
     * Fichier du cache de la requête
     */
    const CACHE_FILE = 'tuya.query';

    /**
     * Cache du Pool de la requête
     * 
     * @var CachePool
     */
    private $queryPool;

    /**
     * Cache du Pool de chaque objet
     * 
     * @var CachePool
     */
    private $devicePool;

    /**
     * ID de l'objet
     * 
     * @var String
     */
    private $deviceID;



    /**
     * Contructeur
     * 
     * @param Session $session
     */
    public function __construct(Session $session)
    {
        $this->queryPool = new CachePool(self::CACHE_FILE);
        $this->devicePool = new CachePool(self::CACHE_FILE);
        $this->namespace = self::NAMESPACE;
        $this->deviceID = null;
        parent::__construct($session);
    }


    /**
     * Affecte l'ID de l'objet pour le fichier de cache
     * 
     * @param Strind $id
     */
    public function setDeviceID($id)
    {
        $this->deviceID = $id;
    }


    /**
     * Requête au Cloud Tuya
     * 
     * @param String $action    : Valeur de l'action à effectuer
     * @param Array  $payload   : Données à envoyer
     * @return Integer : code de retour
     */
    public function request($action = 'QueryDevice', array $payload = [])
    {
        // Change le nom du fichier en fonction de l'ID de l'objet
        if ( $this->deviceID ) $this->devicePool->setFileName( sprintf('tuya.%s', $this->deviceID) );
        // Si mode découverte limité à une seule intérrogation toutes les X minutes
        $this->response = $this->queryPool->fetchFromCache(self::CACHE_DELAY);
        if ( $this->response != null ) {
            $this->response = $this->devicePool->fetchFromCache(999999999);
            return parent::RETURN_INCACHE;
        }

        // Sinon fait la requête au Cloud
        parent::_request($action, $this->namespace, $payload);

        // Sauvegarde dans le cache
        $this->queryPool->storeInCache($this->response);
        $this->devicePool->storeInCache($this->response);

        return ( $this->isSuccess() ) ? parent::RETURN_SUCCESS : parent::RETURN_ERROR;
    }


    /**
     * Retourne les données de l'objet suite à la requête
     * 
     * @return Array
     */
    public function getDatas()
    {
        if ( ! isset($this->response['payload']['data']) ) {
            return null;
        }

        return $this->response['payload']['data'];
    }

}