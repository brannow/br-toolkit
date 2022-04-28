<?php

namespace BR\Toolkit\Typo3\Configuration;

use BR\Toolkit\Typo3\DTO\Configuration\ConfigurationBag;
use BR\Toolkit\Typo3\DTO\Configuration\ConfigurationBagInterface;
use BR\Toolkit\Typo3\VersionWrapper\InstanceUtility;
use TYPO3\CMS\Core\Configuration\ExtensionConfiguration;
use TYPO3\CMS\Core\Configuration\Loader\YamlFileLoader;
use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;
use TYPO3\CMS\Extbase\Object\Exception;

class ConfigurationHandler implements SingletonInterface
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
     * @var array
     */
    private static $configRuntimeCache = [];

    /**
     * @var ConfigurationManagerInterface
     */
    private $configurationManager;

    /**
     * @var YamlFileLoader
     */
    private $yamlFileLoader;

    /**
     * @var ExtensionConfiguration
     */
    private $extensionConfiguration;

    /**
     * ConfigurationHandler constructor.
     * @param ConfigurationManagerInterface|null $configurationManager
     * @param YamlFileLoader|null $yamlFileLoader
     * @param ExtensionConfiguration|null $extensionConfiguration
     * @throws Exception
     */
    public function __construct(
        ConfigurationManagerInterface $configurationManager = null,
        YamlFileLoader $yamlFileLoader = null,
        ExtensionConfiguration $extensionConfiguration = null
    ) {
        $this->yamlFileLoader = $yamlFileLoader ?? InstanceUtility::get(YamlFileLoader::class);
        $this->configurationManager = $configurationManager ?? InstanceUtility::get(ConfigurationManagerInterface::class);
        $this->extensionConfiguration = $extensionConfiguration ?? InstanceUtility::get(ExtensionConfiguration::class);
    }

    /**
     * @param string $extName
     * @return ConfigurationBagInterface
     * @throws \TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationExtensionNotConfiguredException
     * @throws \TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationPathDoesNotExistException
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
     * @param string $pluginName
     * @return ConfigurationBagInterface
     */
    public function getExtensionTypoScript(string $extName, string $pluginName = ''): ConfigurationBagInterface
    {
        $subKey = '_';
        if ($pluginName !== '') {
            $subKey = $pluginName;
        }

        if (!isset(self::$bagCache[$extName][self::TYPE_TS][$subKey])) {
            self::$bagCache[$extName][self::TYPE_TS][$subKey] = new ConfigurationBag($this->getTypoScriptConfigForExtension($extName, $pluginName));
        }

        return self::$bagCache[$extName][self::TYPE_TS][$subKey];
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
     * @param string $extensionPath
     * @param string $excludeRootElement
     * @return ConfigurationBagInterface|null
     */
    public function getYamlConfig(string $extensionPath, string $excludeRootElement = ''): ?ConfigurationBagInterface
    {
        $data = $this->yamlFileLoader->load($extensionPath);
        if (empty($data) || !is_array($data)) return null;

        if ($excludeRootElement !== '') {
            return new ConfigurationBag($data[$excludeRootElement]??$data);
        }

        return new ConfigurationBag($data);
    }

    /**
     * @param string $extName
     * @return array
     * @throws \TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationExtensionNotConfiguredException
     * @throws \TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationPathDoesNotExistException
     */
    private function getGlobalConfigurationForExtension(string $extName): array
    {
        if (empty(self::$configRuntimeCache[$extName])) {
            self::$configRuntimeCache[$extName] = $this->computeConfigurationFiles($extName);
        }

        return (array)(self::$configRuntimeCache[$extName]??[]);
    }

    /**
     * @param string $extName
     * @return array
     * @throws \TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationExtensionNotConfiguredException
     * @throws \TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationPathDoesNotExistException
     */
    private function computeConfigurationFiles(string $extName): array
    {
        return $this->extensionConfiguration->get($extName);
    }

    /**
     * @return array
     */
    private function getTypoScriptConfig(): array
    {
        if (empty(self::$typoScriptRuntimeCache) && $this->configurationManager) {
            self::$typoScriptRuntimeCache = GeneralUtility::removeDotsFromTS($this->configurationManager->getConfiguration(
                ConfigurationManagerInterface::CONFIGURATION_TYPE_FULL_TYPOSCRIPT
            )??[]);
        }

        return self::$typoScriptRuntimeCache;
    }

    /**
     * @param string $extName
     * @param string $pluginName
     * @return array|array[]
     */
    private function getTypoScriptConfigForExtension(string $extName, string $pluginName = ''): array
    {
        $extensionName = str_replace([' ', '_', '-'], '', strtolower($extName));
        if (strpos($extName, 'tx') === false) {
            $extensionName = 'tx_' . $extensionName;
        }

        if ($pluginName !== '') {
            $extensionName .= '_' . $pluginName;
        }

        $config = $this->getTypoScriptConfig();
        return [
            'module' => $config['module'][$extensionName]??[],
            'plugin' => $config['plugin'][$extensionName]??[]
        ];
    }
}
