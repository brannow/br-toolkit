<?php
namespace BR\Toolkit\Typo3\Configuration;

use BR\Toolkit\Typo3\DTO\Configuration\ConfigurationBag;
use BR\Toolkit\Typo3\DTO\Configuration\ConfigurationBagInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;
use TYPO3\CMS\Extbase\Object\ObjectManager;
use TYPO3\CMS\Extbase\Object\Exception;

class ConfigurationHandler
{
    private const TYPE_EXT = 'ext';
    private const TYPE_TS = 'ts';

    /**
     * @var ConfigurationBagInterface[][]
     */
    private static $bagCache = [];

    /**
     * @var array
     */
    private static $typoScriptRuntimeCache = [];

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
    public function getExtensionSetting(string $extName): ConfigurationBagInterface
    {
        if (!isset(self::$bagCache[$extName][self::TYPE_TS])) {
            self::$bagCache[$extName][self::TYPE_TS] = new ConfigurationBag($this->getTypoScriptConfigForExtension($extName));
        }

        return self::$bagCache[$extName][self::TYPE_TS];
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

    /**
     * @param string $extName
     * @return array
     * @throws
     */
    private function getTypoScriptConfigForExtension(string $extName): array
    {
        if (empty(self::$typoScriptRuntimeCache)) {
            try {
                /** @var ObjectManager $om */
                $om = GeneralUtility::makeInstance(ObjectManager::class);
                /** @var ConfigurationManagerInterface $configurationManager */
                $configurationManager = $om->get(ConfigurationManagerInterface::class);
                self::$typoScriptRuntimeCache = GeneralUtility::removeDotsFromTS($configurationManager->getConfiguration(
                    ConfigurationManagerInterface::CONFIGURATION_TYPE_FULL_TYPOSCRIPT
                ));
            } catch (Exception $exception) {}
        }

        $extensionName = str_replace([' ', '_', '-'], '', strtolower($extName));
        if (strpos($extensionName, 'tx_') === false) {
            $extensionName = 'tx_' . $extensionName;
        }

        return [
            'module' => self::$typoScriptRuntimeCache['module'][$extensionName]??[],
            'plugin' => self::$typoScriptRuntimeCache['plugin'][$extensionName]??[]
        ];
    }

}