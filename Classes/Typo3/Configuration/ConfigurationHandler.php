<?php
namespace BR\Toolkit\Typo3\Configuration;

use BR\Toolkit\Typo3\DTO\Configuration\ConfigurationBag;
use BR\Toolkit\Typo3\DTO\Configuration\ConfigurationBagInterface;
use BR\Toolkit\Typo3\VersionWrapper\InstanceUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;
use TYPO3\CMS\Extbase\Object\Exception;

class ConfigurationHandler
{
    private const TYPE_EXT = 'ext';
    private const TYPE_TS = 'ts';
    private const TYPE_GLOBAL_TS = '#global#ts#';

    /**
     * @var ConfigurationBagInterface[][]
     */
    private static $bagCache = [];

    /**
     * @var array
     */
    private static $typoScriptRuntimeCache = [];

    /**
     * @var ConfigurationManagerInterface
     */
    private $configurationManager;

    /**
     * ConfigurationHandler constructor.
     * @param ConfigurationManagerInterface $configurationManager
     */
    public function __construct(ConfigurationManagerInterface $configurationManager = null)
    {
        $this->configurationManager = $configurationManager??InstanceUtility::get(ConfigurationManagerInterface::class);
    }

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
     * @return ConfigurationBagInterface
     */
    public function getExtensionTypoScript(string $extName): ConfigurationBagInterface
    {
        if (!isset(self::$bagCache[$extName][self::TYPE_TS])) {
            self::$bagCache[$extName][self::TYPE_TS] = new ConfigurationBag($this->getTypoScriptConfigForExtension($extName));
        }

        return self::$bagCache[$extName][self::TYPE_TS];
    }

    /**
     * @return ConfigurationBagInterface
     */
    public function getGlobalTypoScript(): ConfigurationBagInterface
    {
        if (!isset(self::$bagCache[self::TYPE_GLOBAL_TS][self::TYPE_TS])) {
            self::$bagCache[self::TYPE_GLOBAL_TS][self::TYPE_TS] = new ConfigurationBag($this->getTypoScriptConfig());
        }

        return self::$bagCache[self::TYPE_GLOBAL_TS][self::TYPE_TS];
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

    private function getTypoScriptConfig(): array
    {
        if (empty(self::$typoScriptRuntimeCache) && $this->configurationManager) {
            try {
                self::$typoScriptRuntimeCache = GeneralUtility::removeDotsFromTS($this->configurationManager->getConfiguration(
                    ConfigurationManagerInterface::CONFIGURATION_TYPE_FULL_TYPOSCRIPT
                ));
            } catch (Exception $exception) {}
        }

        return self::$typoScriptRuntimeCache;
    }

    /**
     * @param string $extName
     * @return array
     * @throws
     */
    private function getTypoScriptConfigForExtension(string $extName): array
    {
        $extensionName = str_replace([' ', '_', '-'], '', strtolower($extName));
        if (strpos($extensionName, 'tx_') === false) {
            $extensionName = 'tx_' . $extensionName;
        }

        $config = $this->getTypoScriptConfig();
        return [
            'module' => $config['module'][$extensionName]??[],
            'plugin' => $config['plugin'][$extensionName]??[]
        ];
    }
}