<?php
/**
 * Classe de la requête de la découverte des objets
 *
 * @author Olivier <sabinus52@gmail.com>
 *
 * @package TuyaCloudApi
 */

namespace Sabinus\TuyaCloudApi\Request;

use Sabinus\TuyaCloudApi\Session\Session;
use Sabinus\TuyaCloudApi\Tools\CachePool;
use Sabinus\TuyaCloudApi\Device\DeviceFactory;


class DiscoveryRequest extends Request implements RequestInterface
{

    /**
     * NameSpace de le requête
     */
    const NAMESPACE = 'discovery';
   
    /**
     * Délai entre 2 requêtes de découverte (restriction Tuya)
     */
    const CACHE_DELAY = 600;

    /**
     * Fichier du cache de la découverte
     */
    const CACHE_FILE = 'tuya.discovery';


    /**
     * Cache du Pool de la découverte
     * 
     * @var CachePool
     */
    private $discoveryPool;

    /**
     * Si resultat venant du cache
     * 
     * @var Integer
     */
    private $isCache;

    

    /**
     * Contructeur
     * 
     * @param Session $session
     */
    public function __construct(Session $session)
    {
        $this->discoveryPool = new CachePool(self::CACHE_FILE);
        $this->isCache = true;
        $this->namespace = self::NAMESPACE;
        parent::__construct($session);
    }


    /**
     * Requête au Cloud Tuya
     * 
     * @param String $action    : Valeur de l'action à effectuer
     * @param Array  $payload   : Données à envoyer
     * @return Integer : code de retour
     */
    public function request($action = 'Discovery', array $payload = [])
    {
        // Si mode découverte limité à une seule intérrogation toutes les X minutes
        $this->response = $this->discoveryPool->fetchFromCache(self::CACHE_DELAY);
        if ( $this->response != null ) return parent::RETURN_INCACHE;

        // Sinon fait la requête au Cloud
        parent::_request($action, $this->namespace, $payload);
        $this->isCache = false;

        // Sauvegarde dans le cache
        $this->discoveryPool->storeInCache($this->response);

        return ( $this->isSuccess() ) ? parent::RETURN_SUCCESS : parent::RETURN_ERROR;
    }


    /**
     * Retourne la liste des objets découverts
     * 
     * @return Array of Device
     */
    public function fetchDevices()
    {
        if ( ! isset($this->response['payload']['devices']) ) {
            return null;
        }

        $result = array();
        foreach ($this->response['payload']['devices'] as $datas) {
            $result[] = DeviceFactory::createDeviceFromDatas($this->session, $datas);
        }

        return $result;
    }


    /**
     * Si le résultat de la requête provient du cache
     * 
     * @return Boolean
     */
    public function isResultInCache()
    {
        return $this->isCache;
    }

}