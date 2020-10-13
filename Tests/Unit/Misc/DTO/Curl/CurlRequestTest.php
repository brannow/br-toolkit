<?php

namespace BR\Toolkit\Tests\Misc\DTO\Curl;

use BR\Toolkit\Misc\DTO\Curl\CurlRequest;
use PHPUnit\Framework\TestCase;

class CurlRequestTest extends TestCase
{
    /**
     * @return array
     */
    public function dataProvider(): array
    {
        return [
            // url, method, query, data
            ['url', 'GET', ['key', 'value'], ['postKey', 'postValue'], 'url?key=value'],
            ['url', 'POST', [], ['postKey', 'postValue'], 'url'],
            ['url', 'GET', ['key', 'value'], [], 'url?key=value'],
            ['url?test=abc&as=as', 'GET', ['key', 'value'], [], 'url?test=abc&as=as&key=value'],
        ];
    }

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
        $request = new CurlRequest();
        $request->setOption($intKey, $value);
        $this->assertSame($value, $request->getOptions()[$intKey]);
    }

    /**
     * @dataProvider dataProvider
     * @param string $url
     * @param string $method
     * @param array $query
     * @param array $postData
     * @param string $fullUrl
     */
    public function testCurlRequestData(string $url, string $method, array $query, array $postData, string $fullUrl)
    {
        $request = new CurlRequest();
        $request->setUrl($url);
        $request->setMethod($method);
        if (!empty($query)) {
            $request->setQuery(...$query);
        }
        if (!empty($postData)) {
            $request->setData(...$postData);
        }

        $this->assertSame($fullUrl, $request->getUrl());
        $this->assertSame($method, $request->getMethod());
        $this->assertSame($method === 'POST', $request->isPost());
        $this->assertSame(http_build_query($request->getData()), $request->getDataString());
        $this->assertSame(http_build_query($request->getQuery()), $request->getQueryString());
    }
}