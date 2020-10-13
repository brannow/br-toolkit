<?php

namespace BR\Toolkit\Tests\Misc\DTO\Curl;

use BR\Toolkit\Misc\DTO\Curl\CurlRequestData;
use PHPUnit\Framework\TestCase;

class CurlRequestDataTest extends TestCase
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
     * @dataProvider dataProvider
     * @param string $url
     * @param string $method
     * @param array $query
     * @param array $postData
     * @param string $fullUrl
     */
    public function testCurlRequestData(string $url, string $method, array $query, array $postData, string $fullUrl)
    {
        $option = new CurlRequestData();
        $option->setUrl($url);
        $option->setMethod($method);
        if (!empty($query)) {
            $option->setQuery(...$query);
        }
        if (!empty($postData)) {
            $option->setData(...$postData);
        }

        $this->assertSame($fullUrl, $option->getUrl());
        $this->assertSame($method, $option->getMethod());
        $this->assertSame($method === 'POST', $option->isPost());
        $this->assertSame(http_build_query($option->getData()), $option->getDataString());
        $this->assertSame(http_build_query($option->getQuery()), $option->getQueryString());
    }
}