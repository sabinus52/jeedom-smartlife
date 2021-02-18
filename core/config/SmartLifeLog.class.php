<?php
/**
 * Classe de fonctions pour l'affichage des logs
 */

use Sabinus\TuyaCloudApi\Device\Device;
use Sabinus\TuyaCloudApi\Request\Request;


class SmartLifeLog
{

    /**
     * Code de retour des requêtes query
     */
    static private $codeQueryCloudTuya = [ 
        Request::RETURN_SUCCESS => 'OK',
        Request::RETURN_INCACHE => 'INCACHE',
        Request::RETURN_ERROR   => 'ERROR'
    ];


    /**
     * Début d'un traitement
     * 
     * @param String $action
     */
    static public function begin($action)
    {
        self::info($action, '=== BEGIN ===================================================');
    }


    /**
     * Fin d'un traitement
     * 
     * @param String $action
     */
    static public function end($action)
    {
        self::info($action, '=== END =====================================================');
    }


    /**
     * Affaiche les données d'un objet
     * 
     * @param String $action
     * @param Device $device
     */
    static public function debugData($action, Device $device)
    {
        self::write('debug', $action, 'Data '.print_r($device->getData(), true), $device);
    }


    /**
     * Affiche le message d'une exception
     * 
     * @param String $action
     * @param Throwable $exception
     */
    static public function exception($action, Throwable $exception)
    {
        self::error($action, $exception->getMessage());
        self::debug($action, print_r($exception, true));
    }


    /**
     * En tête d'un objet
     * 
     * @param String $action
     * @param Device $device
     * @param String $message
     */
    static public function header($action, Device $device, $message = null)
    {
        self::write('info', $action, '-----------------------------------------------------', $device);
        self::write('info', $action, '('.$device->getType().') '.$device->getName(), $device);
        self::debugData($action, $device);
        if ($message) self::write('info', $action, $message, $device, null);
    }


    /**
     * DEBUG
     * 
     * @param String $action
     * @param Device|String $param1
     * @param String|Integer $param2
     * @param Integer $param3
     */
    static public function debug($action, $param1 = null, $param2 = null, $param3 = null)
    {
        $result = self::parseParam($param1, $param2, $param3);
        self::write('debug', $action, $result['message'], $result['device'], $result['coderet']);
    }


    /**
     * INFO
     * 
     * @param String $action
     * @param Device|String $param1
     * @param String|Integer $param2
     * @param Integer $param3
     */
    static public function info($action, $param1 = null, $param2 = null, $param3 = null)
    {
        $result = self::parseParam($param1, $param2, $param3);
        self::write('info', $action, $result['message'], $result['device'], $result['coderet']);
    }


    /**
     * ERROR
     * 
     * @param String $action
     * @param Device|String $param1
     * @param String|Integer $param2
     * @param Integer $param3
     */
    static public function error($action, $param1 = null, $param2 = null, $param3 = null)
    {
        $result = self::parseParam($param1, $param2, $param3);
        self::write('error', $action, $result['message'], $result['device'], $result['coderet']);
    }


    /**
     * Parse les paramètres
     * 
     * @param Device|String $param1
     * @param String|Integer $param2
     * @param Integer $param3
     */
    static private function parseParam($param1 = null, $param2 = null, $param3 = null)
    {
        $result = [
            'device' => $param1,
            'message' => $param2,
            'coderet' => $param3,
        ];
        if ( ! $param1 instanceof Device ) {
            $result['message'] = $param1;
            $result['device'] = null;
        }
        if ( is_integer ($param2) ) {
            $result['coderet'] = $param2;
        }

        return $result;
    }


    /**
     * Ecrire le log
     * 
     * @param String $level
     * @param String $action
     * @param String $message
     * @param Device $device
     * @param Integer $codeReturn
     */
    static private function write($level, $action, $message, Device $device = null, $codeReturn = null)
    {
        log::add('SmartLife', $level, str_repeat(' ', 5-strlen($level)).$action.
            (($device) ? ' '.$device->getId() : '').
            ' : '.    
            $message.
            (( ! is_null($codeReturn) ) ? ' -> (return='.self::$codeQueryCloudTuya[$codeReturn].')'  : '')
        );
    }

}