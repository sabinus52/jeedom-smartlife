<?php
/**
 * Test de la class Color
 *
 * @author Olivier <sabinus52@gmail.com>
 *
 * @package TuyaCloudApi
 */

use PHPUnit\Framework\TestCase;
use Sabinus\TuyaCloudApi\Tools\Color;


class ColorTest extends TestCase
{

    public function testRGB2HEX()
    {
        $this->assertSame('007fff', Color::rgbToHex(array('R' => 0, 'G' => 127, 'B' => 255)));
        $this->assertSame('cf2080', Color::rgbToHex(array('R' => 207, 'G' => 32, 'B' => 128)));
    }


    public function testHEX2RGB()
    {
        $this->assertSame(array('R' => 0, 'G' => 127, 'B' => 255), Color::hexToRgb('007fff'));
        $this->assertSame(array('R' => 207, 'G' => 32, 'B' => 128), Color::hexToRgb('cf2080'));
    }


    public function testHEX2HSL()
    {
        $this->assertSame(array('H' => 210, 'S' => 100, 'L' => 50), Color::hexToHsl('007fff'));
        $this->assertSame(array('H' => 327, 'S' => 73, 'L' => 47), Color::hexToHsl('cf2080'));
    }


    public function testHSL2HEX()
    {
        $this->assertSame('0080ff', Color::hslToHex(array('H' => 210, 'S' => 100, 'L' => 50)));
        $this->assertSame('cf2081', Color::hslToHex(array('H' => 327, 'S' => 73, 'L' => 47)));
    }

}
