<?php

namespace BR\Toolkit\Tests\Typo3\Cache;

use BR\Toolkit\Exceptions\CacheException;
use PHPUnit\Framework\MockObject\MockObject;
use BR\Toolkit\Typo3\Cache\CacheService;
use BR\Toolkit\Typo3\Cache\CacheServiceInterface;
use PHPUnit\Framework\TestCase;
use TYPO3\CMS\Core\Cache\Backend\BackendInterface;
use TYPO3\CMS\Core\Cache\CacheManager;
use TYPO3\CMS\Core\Cache\Frontend\FrontendInterface;

class CacheServiceTest extends TestCase
{
    /**
     * @var CacheServiceInterface
     */
    private $service;

    /**
     * @var MockObject|BackendInterface
     */
    private $cacheAdapter;

    /**
     *
     */
    protected function setUp(): void
    {
        $typoCacheManager = $this->getMockBuilder(CacheManager::class)->disableOriginalConstructor()->getMock();
        $typo3CacheInterface = $this->getMockBuilder(FrontendInterface::class)->getMock();
        $this->cacheAdapter = $typo3BackendCacheAdapter = $this->getMockBuilder(BackendInterface::class)->getMock();


        $typoCacheManager->expects($this->any())->method('getCache')->willReturn($typo3CacheInterface);
        $typo3CacheInterface->expects($this->any())->method('getBackend')->willReturn($typo3BackendCacheAdapter);

        $this->service = new CacheService();

        $property = new \ReflectionProperty(CacheService::class, 'cacheInstance');
        $property->setAccessible(true);
        $property->setValue($this->service, $this->cacheAdapter);

    }

    public function testWriteToCacheSimple()
    {
        $this->cacheAdapter->expects($this->once())
            ->method('set')
            ->with($this->anything(), $this->callback( function (string $data) {
                $container = unserialize($data);

                return $container['randomKey']['content']??false;
            }), [], 0);
        $result = $this->service->cache('randomKey', function () {
            return true;
        });

        $this->assertTrue($result);
    }

    public function testCacheTTLCheck()
    {
        $this->cacheAdapter->expects($this->any())
            ->method('set')
            ->with($this->anything(), $this->anything(), [], 0);
        $result = $this->service->cache('randomKey_TTL', function () {
            return true;
        }, CacheServiceInterface::CONTEXT_GLOBAL . '1', 1);
        $this->assertSame(true, $result);

        $resultNew = $this->service->cache('randomKey_TTL', function () {
            return 3;
        }, CacheServiceInterface::CONTEXT_GLOBAL . '1', 1);
        $this->assertSame(true, $resultNew);

        sleep(2);

        $resultNew = $this->service->cache('randomKey_TTL', function () {
            return 5;
        }, CacheServiceInterface::CONTEXT_GLOBAL . '1', 1);

        $this->assertSame(5, $resultNew);
    }

    public function testExistingCacheEntry()
    {
        $this->cacheAdapter->expects($this->once())
            ->method('get')
            ->willReturn(serialize([
                'randomKey_EXISTS' => [
                    'content' => 3,
                    'ttl' => time() + 3600
                ]
            ]));

        $result = $this->service->cache('randomKey_EXISTS', function () {
            return 5;
        }, 'test_exists', 1);

        $this->assertSame(3, $result);
    }

    public function testInvalidSystemTriggerAsContent()
    {
        $this->expectException(CacheException::class);
        $this->service->cache('randomKey_t', function () {
            return 'NOT_FOUND_TRIGGER_BLOCK_0x011011';
        }, 'testInvalidSystemTriggerAsContent');
    }

    public function testFailedCacheAdapterStorage()
    {
        $this->cacheAdapter->expects($this->once())
            ->method('set')
            ->willThrowException(new \TYPO3\CMS\Core\Cache\Exception('test'));

        $r = $this->service->cache('randomKey', function () {
            return 'broken';
        }, 'testFailedCacheAdapterStorage');

        $this->assertSame('broken', $r);
    }

    public function testDestroyCache()
    {
        $resultDestroy = $this->service->destroy('randomKey', 'testDestroyCache');
        $this->assertFalse($resultDestroy);

        $result = $this->service->cache('randomKey', function () {
            return 'exists1';
        }, 'testDestroyCache', -99);
        $this->assertSame('exists1', $result);

        $result = $this->service->cache('randomKey', function () {
            return 'exists2';
        }, 'testDestroyCache', -1);
        // old result is returned
        $this->assertSame('exists1', $result);

        $this->service->destroy('randomKey', 'testDestroyCache');
        $result = $this->service->cache('randomKey', function () {
            return 'exists2';
        }, 'testDestroyCache', 0);
        // new result is returned
        $this->assertSame('exists2', $result);
    }
    
    public function testCorruptCacheFile()
    {
        $oldErrorLevel = error_reporting();
        // disable notice for the sake of this test, on most prod systems notices are also disabled
        error_reporting(E_ALL & ~E_NOTICE);

        $this->cacheAdapter->expects($this->once())
            ->method('get')
            // set the offset of "s" to an invalid count
            ->willReturn('s:0:"corrupt";');

        $this->cacheAdapter->expects($this->once())
            ->method('remove');
        $this->cacheAdapter->expects($this->once())
            ->method('flush');

        $result = $this->service->cache(
            'test',
            function () {
                return 'success';
            },
            'testCorruptCacheFile'
        );

        error_reporting($oldErrorLevel);
        $this->assertSame('success', $result);
    }

    public function testInvalidCacheFile()
    {
        $oldErrorLevel = error_reporting();
        // disable notice for the sake of this test, on most prod systems notices are also disabled
        error_reporting(E_ALL & ~E_NOTICE);

        $this->cacheAdapter->expects($this->once())
            ->method('get')
            // set the offset of "s" to an invalid count
            ->willReturn('INVALID');

        $this->cacheAdapter->expects($this->once())
            ->method('remove');
        $this->cacheAdapter->expects($this->once())
            ->method('flush');

        $result = $this->service->cache(
            'test',
            function () {
                return 'success';
            },
            'testInvalidCacheFile'
        );

        error_reporting($oldErrorLevel);
        $this->assertSame('success', $result);
    }
}
