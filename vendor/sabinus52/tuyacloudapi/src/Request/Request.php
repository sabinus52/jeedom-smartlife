<?php
/**
 * Classe abstraite de l'objet Request sur les requête au Cloud Tuya
 *
 * @author Olivier <sabinus52@gmail.com>
 *
 * @package TuyaCloudApi
 */

namespace Sabinus\TuyaCloudApi\Request;

use Sabinus\TuyaCloudApi\Session\Session;
use GuzzleHttp\Psr7\Uri;


abstract class Request
{

    /**
     * Code de retour et d'erreur
     */
    const RETURN_SUCCESS = 0;  // Succès de le requête
    const RETURN_INCACHE = 1;  // Resultat récupéré dans le cache
    const RETURN_ERROR   = 9;  // Error de la requête
    const ERROR_NODATA   = 90; // Pas de retour de données
    const ERROR_STATUS   = 91; // Erreur générique
    const ERROR_INVOKE   = 92; // Erreur trop de requêtes fréquentes
   
    /**
     * @var Session
     */
    protected $session;

    /**
     * Réponse de la requête
     * @var Array
     */
    protected $response;

    /**
     * Namespace de le requête
     */
    protected $namespace;



    /**
     * Contructeur
     * 
     * @param Session $session
     */
    public function __construct(Session $session)
    {
        $this->session = $session;
    }


    /**
     * Si la requête est un succès ou pas
     * 
     * @return Boolean
     */
    public function isSuccess()
    {
        return ( isset($this->response['header']['code']) && $this->response['header']['code'] == 'SUCCESS' ) ? true : false;
    }


    /**
     * Retourne la réponse de la requête
     * 
     * @return Array
     */
    public function getResponse()
    {
        return $this->response;
    }


    /**
     * Effectue une requête HTTP dans le Cloud Tuya
     * 
     * @param String $action      : Valeur de l'action à effectuer
     * @param String $namespace : Espace de nom
     * @param Array  $payload   : Données à envoyer
     * @return Array
     */
    protected function _request($action, $namespace, array $payload = [])
    {
        $token = $this->session->getToken();
        if (!$token) return null;

        $this->response = $this->session->getClient()->post(new Uri('/homeassistant/skill'), array(
            'json' => array(
                'header' => array(
                    'name'           => $action,
                    'namespace'      => $namespace,
                    'payloadVersion' => 1,
                ),
                    'payload' => $payload + array(
                    'accessToken'    => $token,
                ),
            ),
        ));
        $this->response = json_decode((string) $this->response->getBody(), true);
        $this->checkResponse(sprintf('Failed to get "%s" response from Cloud Tuya', $action));

        return $this->response;
    }


    /**
     * Vérifie si pas d'erreur dans le retour de la requête
     * 
     * @param String $message : Message par défaut
     * @throws Exception
     */
    private function checkResponse($message = null)
    {
       // print_r($this->response); // FIXME
        if ( empty($this->response) ) {
            throw new \Exception($message.' : Datas return null', self::ERROR_NODATA);
        }
        if ( isset($this->response['responseStatus']) && $this->response['responseStatus'] === 'error' ) {
            $message = isset($this->response['errorMsg']) ? $this->response['errorMsg'] : $message;
            throw new \Exception($message, self::ERROR_STATUS);
        }
        if ( isset($this->response['header']['code']) && $this->response['header']['code'] == 'FrequentlyInvoke' ) {
            $message = isset($this->response['header']['msg']) ? $this->response['header']['msg'] : $message;
            throw new \Exception($message, self::ERROR_INVOKE);
        }
    }

}