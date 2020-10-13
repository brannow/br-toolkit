<?php

namespace BR\Toolkit\Tests\Typo3\DTO\Configuration;

use BR\Toolkit\Typo3\DTO\Configuration\ConfigurationBag;
use PHPUnit\Framework\TestCase;

class ConfigurationBagTest extends TestCase
{
    public function testEmptyConfig()
    {
        $bag = new ConfigurationBag([]);
        $this->assertSame('', $bag->getValue('test'));
        $this->assertSame('test', $bag->getValue('test', 'test'));
        $this->assertSame('fallback', $bag->getValueFromArrayPath('test#test#test', 'fallback', '#'));
        $this->assertSame([], $bag->getExplodedIntValue('test', ','));
        $this->assertSame([], $bag->getExplodedIntValueFromArrayPath('test#test#test', '#'));
    }

    public function testGetValueConfig()
    {
        $bag = new ConfigurationBag(['key' => 'success']);
        $this->assertSame('success', $bag->getValue('key'));
    }

    public function testGetValueFromArrayPathConfig()
    {
        $bag = new ConfigurationBag(['key' => ['sub' => 'success']]);
        $this->assertSame('success', $bag->getValueFromArrayPath('key.sub'));
        $this->assertSame('success', $bag->getValueFromArrayPath('key-sub', '', '-'));
    }

    public function testGetExplodedIntValueConfig()
    {
        $bag = new ConfigurationBag(['key' => '1,2,3,4']);
        $this->assertSame([1,2,3,4], $bag->getExplodedIntValue('key'));

        $bag = new ConfigurationBag(['key' => '5|6|7']);
        $this->assertSame([5,6,7], $bag->getExplodedIntValue('key', '|'));
    }

    public function testGetExplodedIntValueNonNumberConfig()
    {
        $bag = new ConfigurationBag(['key' => 'a,v,x']);
        $this->assertSame([], $bag->getExplodedIntValue('key'));
    }

    public function testGetExplodedIntValueInvalidValueConfig()
    {
        $bag = new ConfigurationBag(['key' => new \DateTime()]);
        $this->assertSame([], $bag->getExplodedIntValue('key'));
    }

    public function testGetExplodedIntValueFromArrayPathConfig()
    {
        $bag = new ConfigurationBag(['key' => ['sub' => '1,2,3,4']]);
        $this->assertSame([1,2,3,4], $bag->getExplodedIntValueFromArrayPath('key.sub'));
        $bag = new ConfigurationBag(['key' => ['sub' => '1|2|3|4']]);
        $this->assertSame([1,2,3,4], $bag->getExplodedIntValueFromArrayPath('key-sub', '-', '|'));
    }

    public function testGetExplodedIntValueFromArrayPathInvalidValueConfig()
    {
        $bag = new ConfigurationBag(['key' => ['sub' => new \DateTime()]]);
        $this->assertSame([], $bag->getExplodedIntValueFromArrayPath('key.sub'));
    }
}