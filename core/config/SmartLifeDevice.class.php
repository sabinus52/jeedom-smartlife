<?php
/**
 * Classe d'un équipement SmartLife
 */

use Sabinus\TuyaCloudApi\Device\Device;
use Sabinus\TuyaCloudApi\Device\DeviceFactory;
use Sabinus\TuyaCloudApi\Tools\Color;


class SmartLifeDevice
{

    /**
     * Objet du device
     * 
     * @param Device
     */
    private $device;


    /**
     * Constructeur
     * 
     * @param Device $device
     */
    public function __construct(Device $device)
    {
        $this->device = $device;
    }


    /**
     * Appele la fonction d'évènement pour un équipement SamrtLife
     * 
     * @param String $functionName : Nom de la fonction de l'évènement
     * @param Array $params : Paramètres de la fonction $functionName
     * @param String $msgLog : Message de log en rapport à la fonction
     */
    public function callFunctionEvent($functionName, array $params, $actionLog)
    {
        $retry = 3;
        while ($retry > 0) {
            $retry--;
            SmartLifeLog::debug($actionLog, $this->device, 'Tentative '.(3-$retry).' - '.$functionName.'('.implode(',', $params).')');
            try {
                $result = call_user_func_array( array($this->device, $functionName), $params );
                $retry = 0;
            } catch (Throwable $th) {
                log::add('SmartLife', 'debug', 'Erreur de connexion au cloud Tuya : '.$th->getMessage());
                if ($retry > 0) continue;
                log::add('SmartLife', 'debug', $msgLog.' : '.print_r($th, true));
                log::add('SmartLife', 'error', 'Erreur de connexion au cloud Tuya : '.$th->getMessage());
                throw new Exception(__('Erreur de connexion au cloud Tuya : '.$th->getMessage(),__FILE__));
            }
        }
        return $result;
    }


    /**
     * Retourne la valeur d'un paramètre d'un équipement Tuya
     * 
     * @param String $cmdInfo : Nom de la commande info = nom du paramètre Tuya
     * @param String|Integer
     */
    public function getValueCommandInfo($cmdInfo)
    {
        switch ($cmdInfo) {
            /*case 'COLORHUE' :
                return null; // HACK API Tuya ne retourne plus le statut sur la couleur
                //$value = array('H' => $this->device->getColorHue(), 'S' => $this->device->getColorSaturation(), 'L' => $this->device->getBrightness());
                //return  '#'.Color::hslToHex($value);
                break;
            case 'SATURATION' :
                return null; // HACK API Tuya ne retourne plus le statut sur la saturation
            case 'TEMPERATURE' :
                if ( $this->device->getType() == DeviceFactory::TUYA_LIGHT ) return null; // HACK API Tuya ne retourne plus le statut sur la température pour les lampes*/
            case 'STATE' :
                $value = $this->device->getState();
                if ( $this->device->getType() == DeviceFactory::TUYA_COVER ) {
                    switch ($value) {
                        case 3 : return 1; // Entre ouvert
                        case 2 : return 0; // Fermé
                        case 1 : return 2; // Ouvert
                        default: return $value;
                    }
                } else {
                    return $value;
                }
                break;
            default:
                //if ( is_callable( array($this->device, 'get'.ucfirst($cmdInfo)) ) )
                return call_user_func( array($this->device, 'get'.ucfirst($cmdInfo)) );
                break;
        }

        return null;
    }

}
