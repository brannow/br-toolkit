<?php
namespace BR\Toolkit\Typo3\Configuration;

use BR\Toolkit\Typo3\DTO\Configuration\ConfigurationBag;
use BR\Toolkit\Typo3\DTO\Configuration\ConfigurationBagInterface;
use BR\Toolkit\Typo3\VersionWrapper\InstanceUtility;
use TYPO3\CMS\Core\Configuration\Loader\YamlFileLoader;
use TYPO3\CMS\Core\Core\Environment;
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
     * ConfigurationHandler constructor.
     * @param ConfigurationManagerInterface|null $configurationManager
     */
    public function __construct(ConfigurationManagerInterface $configurationManager = null, YamlFileLoader $yamlFileLoader = null)
    {
        $this->yamlFileLoader = $yamlFileLoader ?? InstanceUtility::get(YamlFileLoader::class);
        $this->configurationManager = $configurationManager ?? InstanceUtility::get(ConfigurationManagerInterface::class);
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
     */
    private function getGlobalConfigurationForExtension(string $extName): array
    {
        if (empty(self::$configRuntimeCache)) {
            self::$configRuntimeCache = $this->computeConfigurationFiles();
        }

        return (array)(self::$configRuntimeCache[$extName]??[]);
    }

    /**
     *
     */
    private function computeConfigurationFiles(): array
    {
        if (!isset($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf'])) {
            // load local and additional
            return $this->loadConfigFiles();
        }

        $data = [];
        foreach ($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf'] as $extKey => $value) {
            $v = unserialize($value);
            if ($v !== false) {
                $data[$extKey] = $v;
            }
        }

        return $data;
    }

    /**
     * @return array
     */
    private function loadConfigFiles(): array
    {
        $confPath = Environment::getPublicPath() . DIRECTORY_SEPARATOR . 'typo3conf';
        $localConfig = $confPath . DIRECTORY_SEPARATOR . 'LocalConfiguration.php';
        if (file_exists($localConfig)) {
            $config = require $localConfig;
            $GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf'] = $config['EXTENSIONS']??[];

            $additionConfig = $confPath . DIRECTORY_SEPARATOR . 'AdditionalConfiguration.php';
            if (file_exists($additionConfig)) {
                require $additionConfig;
            }
        }

        return $GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']??[];
    }

    /**
     * @return array
     */
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