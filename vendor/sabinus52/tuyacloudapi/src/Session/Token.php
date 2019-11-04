<?php
/**
 * Token de la session
 *
 * @author Olivier <sabinus52@gmail.com>
 *
 * @package TuyaCloudApi
 */

namespace Sabinus\TuyaCloudApi\Session;


class Token
{

    /**
     * Jeton principal
     * 
     * @param String
     */
	private $tokenAccess;

    /**
     * Jeton pour la rafraichissement
     * 
     * @param String
     */
    private $tokenRefresh;

    /**
     * Temps d'expiration en secondes
     * 
     * @param Integer
     */
    private $expireTime;



    /**
     * Retourne la valeur du jeton
     * 
     * @return String
     */
    public function get()
    {
        return $this->tokenAccess;
    }

    
    /**
     * Retourne le jeton de rafraichissement
     * 
     * @return String
     */
    public function getTokenRefresh()
    {
        return $this->tokenRefresh;
    }


    /**
     * Affecte les données du jeton retourné dans l'objet
     */
    public function set(array $data)
    {
        $this->tokenAccess = $data['access_token'];
        $this->tokenRefresh = $data['refresh_token'];
        $this->expireTime = $data['expires_in'] + time();
    }


    /**
     * S'il y a un jeton
     * 
     * @return Boolean
     */
    public function has()
    {
        if ( $this->tokenAccess && $this->tokenRefresh && $this->expireTime )
            return true;
        else
            return false;
    }

    
    /**
     * Si le jeton est encore valide
     * 
     * @return Boolean
     */
    public function isValid()
    {
        return $this->expireTime > time();
    }

}