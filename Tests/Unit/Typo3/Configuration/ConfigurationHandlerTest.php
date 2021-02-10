<?php

namespace BR\Toolkit\Tests\Typo3\Configuration;

use BR\Toolkit\Typo3\Configuration\ConfigurationHandler;
use PHPUnit\Framework\TestCase;

class ConfigurationHandlerTest extends TestCase
{
    public function setUp(): void
    {
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
        $handler = new ConfigurationHandler();
        $name = $handler->getExtensionConfiguration('test_ext')->getValue('name');
        $bool = $handler->getExtensionConfiguration('test_ext')->getValue('bool');
        $int = $handler->getExtensionConfiguration('test_ext')->getValue('int');
        $float = $handler->getExtensionConfiguration('test_ext')->getValue('float');
        $intList = $handler->getExtensionConfiguration('test_ext')->getExplodedIntValueFromArrayPath('sub.list');

        $this->assertSame('test', $name);
        $this->assertSame(true, $bool);
        $this->assertSame(4, $int);
        $this->assertSame(2.3, $float);
        $this->assertSame([99,5,1233], $intList);
    }

    public function testExtConfigLoadNotFound()
    {
        $handler = new ConfigurationHandler();
        $name = $handler->getExtensionConfiguration('test_ext_notFound')->getValue('name');
        $this->assertSame('', $name);
    }
}