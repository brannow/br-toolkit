<?php

namespace BR\Toolkit\Tests\Misc\DTO\Curl;

use BR\Toolkit\Misc\DTO\Curl\CurlRequestOptions;
use PHPUnit\Framework\TestCase;

class CurlRequestOptionsTest extends TestCase
{
    /**
     * @return array
     */
    public function optionProvider(): array
    {
        return [
            // key, value
            ['a', true],
            [1, false],
            ['1', 'a'],
            ['test', 12.2]
        ];
    }

    /**
     * @dataProvider optionProvider
     * @param $key
     * @param $value
     */
    public function testCurlRequestOptions($key, $value)
    {
        $intKey = (int)$key;
        $options = new CurlRequestOptions();
        $options->setOption($intKey, $value);

        $this->assertSame($value, $options->getOptions()[$intKey]);
    }
}