<?php
/**
 * Classe de fonctions pour l'affichage des logs
 */

use Sabinus\TuyaCloudApi\Device\Device;


class SmartLifeLog
{


    static public function header($action, Device $device, $message)
    {
        self::write('debug', $action, '-----------------------------------------------------', $device);
        self::write('debug', $action, '('.$device->getType().') '.$device->getName(), $device);
        self::write('debug', $action, $message, $device);
    }

    static public function debug($action, $param1 = null, $param2 = null)
    {
        if ( $param1 instanceof Device ) {
            self::write('debug', $action, $param2, $param1);
        } else {
            self::write('debug', $action, $param1, null);
        }
    }

    static private function write($level, $action, $message, Device $device = null)
    {
        log::add('SmartLife', $level, $action.(($device) ? ' '.$device->getId() : '').' : '.$message);
    }

}