<?php
/**
 * Test de la class TokenPool
 *
 * @author Olivier <sabinus52@gmail.com>
 *
 * @package TuyaCloudApi
 */

use PHPUnit\Framework\TestCase;
use Sabinus\TuyaCloudApi\Tools\TokenPool;


class TokenPoolTest extends TestCase
{

    public function testStoreToken()
    {
        $pool = new TokenPool();
        $this->assertSame(null, $pool->fetchTokenFromCache());
        $pool->storeTokenInCache([0,1,2]);
        $this->assertSame([0,1,2], $pool->fetchTokenFromCache());
        $pool->clearFromCache();
        $this->assertSame(null, $pool->fetchTokenFromCache());
    }


    public function testStoreOtherFolderToken()
    {
        $pool = new TokenPool();
        $pool->setFolder('/var/tmp');
        $pool->storeTokenInCache([1,2,3]);
        $this->assertSame([1,2,3], $pool->fetchTokenFromCache());
        $pool->clearFromCache();
        $this->assertSame(null, $pool->fetchTokenFromCache());
    }

}
