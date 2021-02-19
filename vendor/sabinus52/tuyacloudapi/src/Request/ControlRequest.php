<?php
/**
 * Classe de la requête pour le controle d'un objet
 *
 * @author Olivier <sabinus52@gmail.com>
 *
 * @package TuyaCloudApi
 */

namespace Sabinus\TuyaCloudApi\Request;

use Sabinus\TuyaCloudApi\Session\Session;


class ControlRequest extends Request implements RequestInterface
{
    /**
     * NameSpace de le requête
     */
    const NAMESPACE = 'control';


    /**
     * Contructeur
     * 
     * @param Session $session
     */
    public function __construct(Session $session)
    {
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
    public function request($action, array $payload = [])
    {
        parent::_request($action, $this->namespace, $payload);

        return ( $this->isSuccess() ) ? parent::RETURN_SUCCESS : parent::RETURN_ERROR;
    }

}