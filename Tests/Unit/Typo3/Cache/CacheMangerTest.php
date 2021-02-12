<?php

namespace BR\Toolkit\Tests\Typo3\Cache;

use PHPUnit\Framework\TestCase;
use BR\Toolkit\Typo3\Cache\CacheManager;

class CacheMangerTest extends TestCase
{
    public function testRegisterDefaultCacheConfig()
    {
        \BR\Toolkit\Typo3\Cache\CacheManager::registerCache(CacheManager::CACHE_OPTION_FILE);
        $this->assertNotEmpty($GLOBALS['TYPO3_CONF_VARS']['SYS']['caching']['cacheConfigurations'][CacheManager::CACHE_DOMAIN]);
    }
}