<?php
namespace BR\Toolkit\Typo3\Configuration;

use BR\Toolkit\Typo3\DTO\Configuration\ConfigurationBag;
use BR\Toolkit\Typo3\DTO\Configuration\ConfigurationBagInterface;

class ConfigurationHandler
{
    private const TYPE_EXT = 'ext';

    /**
     * @var ConfigurationBagInterface[][]
     */
    private static $bagCache = [];

    /**
     * @param string $extName
     * @return ConfigurationBagInterface
     */
    public function getExtensionConfiguration(string $extName): ConfigurationBagInterface
    {
        if (!isset(self::$bagCache[$extName][self::TYPE_EXT])) {
            self::$bagCache[$extName][self::TYPE_EXT] = new ConfigurationBag($this->getGlobalConfigurationForExtension($extName));
        }

        return self::$bagCache[$extName][self::TYPE_EXT];
    }

    /**
     * @param string $extName
     * @return array
     */
    private function getGlobalConfigurationForExtension(string $extName): array
    {
        $settings = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf'][$extName]??'');
        if (!$settings) {
            return [];
        }

        return $settings;
    }

}