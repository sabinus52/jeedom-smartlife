<?php
/**
 * Librairie des transformations des couleurs en surcharge de la classe Color
 *
 * @author Olivier <sabinus52@gmail.com>
 *
 * @package TuyaCloudApi
 * @link https://github.com/mexitek/phpColors
 */

namespace Sabinus\TuyaCloudApi\Tools;

use Mexitek\PHPColors\Color as MexitekColor;


class Color extends MexitekColor
{

    /**
     * Corrections pour retourner les valeurs en pourcentage sur la saturation et la luminosité
     * 
     * @see Mexitek\PHPColors\Color::hexToHsl
     */
    public static function hexToHsl($color)
    {
        $HSL = parent::hexToHsl($color);

        // Correction en pourcentage
        $HSL['H'] = (int) $HSL['H'];
        $HSL['S'] = (int) round($HSL['S']*100);
        $HSL['L'] = (int) round($HSL['L']*100);

        return $HSL;
    }


    /**
     * Couvertion des valeurs de la saturation et la luminosité de pourcentage en décimale
     * 
     * @see Mexitek\PHPColors\Color::hslToHex
     */
    public static function hslToHex($hsl = array())
    {
        // Correction en decimal
        if (isset($hsl['S'])) $hsl['S'] = $hsl['S'] / 100;
        if (isset($hsl['L'])) $hsl['L'] = $hsl['L'] / 100;

        return parent::hslToHex($hsl);
   }

}
