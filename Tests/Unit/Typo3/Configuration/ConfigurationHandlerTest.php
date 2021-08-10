<?php

namespace BR\Toolkit\Tests\Typo3\Configuration;

use BR\Toolkit\Misc\Native\FileHandler;
use BR\Toolkit\Typo3\Cache\CacheService;
use BR\Toolkit\Typo3\Configuration\ConfigurationHandler;
use BR\Toolkit\Typo3\DTO\Configuration\ConfigurationBagInterface;
use PHPUnit\Framework\TestCase;
use TYPO3\CMS\Core\Configuration\ExtensionConfiguration;
use TYPO3\CMS\Core\Configuration\Loader\Exception\YamlFileLoadingException;
use TYPO3\CMS\Core\Configuration\Loader\YamlFileLoader;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;
use TYPO3\CMS\Extbase\Object\Exception;

class ConfigurationHandlerTest extends TestCase
{
    /**
     * @var ConfigurationHandler
     */
    private $handler;

    public static $cacheDataHandle;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject|ConfigurationManagerInterface
     */
    private $typoConfigManager;

    /**
     * @var ExtensionConfiguration|\PHPUnit\Framework\MockObject\MockObject
     */
    private $sysConfig;

    /**
     * @var YamlFileLoader|\PHPUnit\Framework\MockObject\MockObject
     */
    private $yamlLoader;

    /**
     * @var \ReflectionProperty|\ReflectionProperty|null
     */
    private ?\ReflectionProperty $reflectionConfigRuntimeCache = null;

    /**
     * @var \ReflectionProperty|\ReflectionProperty|null
     */
    private ?\ReflectionProperty $reflectionBagCache = null;

    /**
     * @var \ReflectionProperty|\ReflectionProperty|null
     */
    private ?\ReflectionProperty $reflectionTypoScriptRuntimeCache = null;

    public function setUp(): void
    {
        $property = new \ReflectionProperty(ConfigurationHandler::class, 'configRuntimeCache');
        $property->setAccessible(true);
        $this->reflectionConfigRuntimeCache = $property;

        $property = new \ReflectionProperty(ConfigurationHandler::class, 'bagCache');
        $property->setAccessible(true);
        $this->reflectionBagCache = $property;

        $property = new \ReflectionProperty(ConfigurationHandler::class, 'typoScriptRuntimeCache');
        $property->setAccessible(true);
        $this->reflectionTypoScriptRuntimeCache = $property;

        $this->typoConfigManager = $this->getMockBuilder(ConfigurationManagerInterface::class)->getMock();
        $this->fileHandler = $this->getMockBuilder(FileHandler::class)->getMock();
        $this->sysConfig = $this->getMockBuilder(ExtensionConfiguration::class)->getMock();
        $this->yamlLoader = $this->getMockBuilder(YamlFileLoader::class)->getMock();
        $this->handler = new ConfigurationHandler($this->typoConfigManager, $this->yamlLoader, $this->sysConfig);
    }

    public function testExtConfigLoadSuccess()
    {
        $this->sysConfig->expects($this->once())
            ->method('get')
            ->with('test_ext')
            ->willReturn([
                'version' => '1.0',
                'name' => 'test',
                'sub' => [
                    'list' => '99,5,1233'
                ],
                'bool' => true,
                'int' => 4,
                'float' => 2.3
            ]);

        $name = $this->handler->getExtensionConfiguration('test_ext')->getValue('name');
        $bool = $this->handler->getExtensionConfiguration('test_ext')->getValue('bool');
        $int = $this->handler->getExtensionConfiguration('test_ext')->getValue('int');
        $float = $this->handler->getExtensionConfiguration('test_ext')->getValue('float');
        $intList = $this->handler->getExtensionConfiguration('test_ext')->getExplodedIntValueFromArrayPath('sub.list');

        $this->assertSame('test', $name);
        $this->assertSame(true, $bool);
        $this->assertSame(4, $int);
        $this->assertSame(2.3, $float);
        $this->assertSame([99,5,1233], $intList);
    }

    public function testExtConfigLoadNotFound()
    {
        $this->sysConfig->expects($this->exactly(2))
            ->method('get')
            ->willReturn([]);

        $name = $this->handler->getExtensionConfiguration('test_ext_notFound')->getValue('name');
        $this->assertSame('', $name);

        $this->reflectionConfigRuntimeCache->setValue($this->handler, []);
        $name = $this->handler->getExtensionConfiguration('test_ext_notFound2')->getValue('name');
        $this->assertSame('', $name);
    }

    public function testGlobalTypoScriptGeneric()
    {
        $data = $this->handler->getGlobalTypoScript();
        $this->assertTrue($data instanceof ConfigurationBagInterface);
        $this->assertSame($data->getData(), []);
    }

    public function testGlobalTypoScriptTSFound()
    {
        $this->reflectionBagCache->setValue($this->handler, []);
        $this->typoConfigManager->expects($this->once())
            ->method('getConfiguration')
            ->willReturn([
                'test' => 'PAGE',
                'test.' => [
                    'sampleData' => 1,
                    '10' => 'USER',
                    '10.' => [
                        'controller' => 'abc'
                    ]
                ]
            ]);

        $data = $this->handler->getGlobalTypoScript();
        $this->assertTrue($data instanceof ConfigurationBagInterface);
        $this->assertSame($data->getData(), [
            'test' => [
                'sampleData' => 1,
                '10' => [
                    'controller' => 'abc'
                ]
            ]
        ]);
    }

    public function testTSLoadingException()
    {
        $this->reflectionTypoScriptRuntimeCache->setValue($this->handler, []);
        $this->reflectionBagCache->setValue($this->handler, []);
        $this->typoConfigManager->expects($this->once())
            ->method('getConfiguration')
            ->willThrowException(new Exception('TEST'));
        $r = $this->handler->getExtensionTypoScript('test_ext_notFound');
        $this->assertEquals(['module' => [], 'plugin' => []], $r->getData());
    }

    public function testTSLoading()
    {
        $r = $this->handler->getExtensionTypoScript('test_ext_notFound');
        $this->assertEquals(['module' => [], 'plugin' => []],$r->getData());
    }

    public function testYamlLoadingNotFound()
    {
        $this->expectException(YamlFileLoadingException::class);
        $this->yamlLoader->expects($this->once())
            ->method('load')
            ->willThrowException(new YamlFileLoadingException('error', 0));
        $this->handler->getYamlConfig('EXT:bmub_sitepackage/Configuration/NOTFOUND.yaml', 'config');
    }

    public function testYamlLoading()
    {
        $path = 'EXT:bmub_sitepackage/Configuration/NOTFOUND.yaml';
        $this->yamlLoader->expects($this->once())
            ->method('load')
            ->with($path)
            ->willReturn(
                [
                    'config' => [
                        'a' => [
                            'b' => 'c'
                        ]
                    ]
                ]
            );
        $result = $this->handler->getYamlConfig($path, 'config');
        $this->assertSame([
            'a' => [
                'b' => 'c'
            ]
        ],
        $result->getData());
    }

    public function testYamlLoadingWithoutCutOff()
    {
        $path = 'EXT:bmub_sitepackage/Configuration/NOTFOUND.yaml';
        $this->yamlLoader->expects($this->once())
            ->method('load')
            ->with($path)
            ->willReturn(
                [
                    'config' => [
                        'a' => [
                            'b' => 'c'
                        ]
                    ]
                ]
            );
        $result = $this->handler->getYamlConfig($path);
        $this->assertSame([
            'config' => [
                'a' => [
                    'b' => 'c'
                ]
            ]
        ],
            $result->getData());
    }
}