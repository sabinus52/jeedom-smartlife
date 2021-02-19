<?php
/**
 * Interface de l'ojet Request
 *
 * @author Olivier <sabinus52@gmail.com>
 *
 * @package TuyaCloudApi
 */

namespace Sabinus\TuyaCloudApi\Request;

use Sabinus\TuyaCloudApi\Session\Session;


interface RequestInterface
{ 

    /**
     * Contructeur
     * 
     * @param Session $session
     */
    public function __construct(Session $session);

    /**
     * Requête au Cloud Tuya
     * 
     * @param String $action    : Valeur de l'action à effectuer
     * @param Array  $payload   : Données à envoyer
     * @return Integer : code de retour
     */
    public function request($action, array $payload = []);

}