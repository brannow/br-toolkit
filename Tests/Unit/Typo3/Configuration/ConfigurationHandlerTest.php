<?php

namespace BR\Toolkit\Tests\Typo3\Configuration;

use BR\Toolkit\Typo3\Configuration\ConfigurationHandler;
use PHPUnit\Framework\TestCase;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;
use TYPO3\CMS\Extbase\Object\Exception;

class ConfigurationHandlerTest extends TestCase
{
    /**
     * @var ConfigurationHandler
     */
    private $handler;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject|ConfigurationManagerInterface
     */
    private $typoConfigManager;

    public function setUp(): void
    {
        $this->typoConfigManager = $this->getMockBuilder(ConfigurationManagerInterface::class)->getMock();
        $this->handler = new ConfigurationHandler($this->typoConfigManager);

        $config = [
            'version' => '1.0',
            'name' => 'test',
            'sub' => [
                'list' => '99,5,1233'
            ],
            'bool' => true,
            'int' => 4,
            'float' => 2.3
        ];

        // typo3 ext config
        $GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['test_ext'] = serialize($config);
    }

    public function testExtConfigLoadSuccess()
    {
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

        $name = $this->handler->getExtensionConfiguration('test_ext_notFound')->getValue('name');
        $this->assertSame('', $name);
    }

    public function testTSLoadingException()
    {
        $this->typoConfigManager->expects($this->once())
            ->method('getConfiguration')
            ->willThrowException(new Exception('TEST'));
        $r = $this->handler->getExtensionTypoScript('test_ext_notFound');
        $this->assertEquals(['module' => [], 'plugin' => []],$r->getData());
    }

    public function testTSLoading()
    {
        $r = $this->handler->getExtensionTypoScript('test_ext_notFound');
        $this->assertEquals(['module' => [], 'plugin' => []],$r->getData());
    }
}