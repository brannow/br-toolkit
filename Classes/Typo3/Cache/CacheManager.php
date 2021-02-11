<?php
namespace BR\Toolkit\Typo3\Cache;

use TYPO3\CMS\Core\Cache\Backend\FileBackend;
use TYPO3\CMS\Core\Cache\Frontend\VariableFrontend;

class CacheManager
{
    public const CACHE_DOMAIN = 'br_toolkit';
    public const CACHE_OPTION_FILE = 1;

    public static $cacheOptions = [
        self::CACHE_OPTION_FILE => FileBackend::class
    ];

    /**
     * @param int $cacheOption
     */
    public static function registerCache(int $cacheOption = self::CACHE_OPTION_FILE): void
    {
        $GLOBALS['TYPO3_CONF_VARS']['SYS']['caching']['cacheConfigurations'][static::CACHE_DOMAIN] = [
            'frontend' => VariableFrontend::class,
            'backend' => static::$cacheOptions[$cacheOption]??static::$cacheOptions[static::CACHE_OPTION_FILE],
            'groups' => [
                'all',
                'system'
            ],
        ];
    }
}