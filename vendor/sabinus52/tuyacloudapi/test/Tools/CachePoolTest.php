<?php
/**
 * Test de la class TokenPool
 *
 * @author Olivier <sabinus52@gmail.com>
 *
 * @package TuyaCloudApi
 */

use PHPUnit\Framework\TestCase;
use Sabinus\TuyaCloudApi\Tools\CachePool;


class CachePoolTest extends TestCase
{

    public function testStoreCache()
    {
        $pool = new CachePool('cachepool.test');
        $pool->clearFromCache();
        $this->assertSame(null, $pool->fetchFromCache());
        $pool->storeInCache([0,1,2]);
        $this->assertSame(null, $pool->fetchFromCache(0));
        $this->assertSame([0,1,2], $pool->fetchFromCache(3)); // Non périmé
        sleep(5);
        $this->assertSame(null, $pool->fetchFromCache(3)); // Périmé
        $pool->clearFromCache();
        $this->assertSame(null, $pool->fetchFromCache(3));
    }


    public function testStoreOtherFolderCache()
    {
        $pool = new CachePool('cachepool.test');
        $pool->setFolder('/var/tmp');
        $pool->clearFromCache();
        $pool->storeInCache([1,2,3]);
        $this->assertSame(null, $pool->fetchFromCache());
        $this->assertSame([1,2,3], $pool->fetchFromCache(3));
        $pool->clearFromCache();
        $this->assertSame(null, $pool->fetchFromCache());
    }


    public function testStoreOtherFileNameCache()
    {
        $pool = new CachePool('cachepool.test');
        $pool->clearFromCache();
        $pool->storeInCache([1,2,3]);
        $this->assertSame([1,2,3], $pool->fetchFromCache(10));
        $pool->setFileName('cachepool.test2');
        $this->assertSame(null, $pool->fetchFromCache(10));
        $pool->storeInCache([4,5,6]);
        $this->assertSame([4,5,6], $pool->fetchFromCache(10));
        $pool->clearFromCache();
        $pool->setFileName('cachepool.test');
        $this->assertSame([1,2,3], $pool->fetchFromCache(10));
    }

}
