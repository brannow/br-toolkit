<?php
namespace BR\Toolkit\Typo3\Cache;

use TYPO3\CMS\Core\Cache\Frontend\FrontendInterface;
use TYPO3\CMS\Core\Cache\Frontend\VariableFrontend;
use TYPO3\CMS\Core\Cache\CacheManager as BaseCacheManager;

class CacheManager extends BaseCacheManager
{
    public const CACHE_DOMAIN = 'br_toolkit';
    public const CACHE_OPTION_FILE = 1;

    public static $cacheOptions = [
        self::CACHE_OPTION_FILE => SecureFileBackend::class
    ];

    /**
     * @param int $cacheOption
     * @return array
     */
    public static function announceCache(int $cacheOption = self::CACHE_OPTION_FILE): array
    {
        return $GLOBALS['TYPO3_CONF_VARS']['SYS']['caching']['cacheConfigurations'][static::CACHE_DOMAIN] = [
            'frontend' => VariableFrontend::class,
            'backend' => static::$cacheOptions[$cacheOption]??static::$cacheOptions[static::CACHE_OPTION_FILE],
            'groups' => [
                'all',
                'system'
            ],
        ];
    }

    /**
     * @param string $identifier
     * @param array $config
     * @return FrontendInterface
     * @throws \TYPO3\CMS\Core\Cache\Exception\DuplicateIdentifierException
     * @throws \TYPO3\CMS\Core\Cache\Exception\InvalidBackendException
     * @throws \TYPO3\CMS\Core\Cache\Exception\InvalidCacheException
     */
    public function sideLoadCacheFrontend(string $identifier, array $config): ?FrontendInterface
    {
        $this->setCacheConfigurations([
            $identifier => $config
        ]);

        if (!isset($this->caches[$identifier])) {
            $this->createCache($identifier);
        }
        return $this->caches[$identifier]??null;
    }
}
