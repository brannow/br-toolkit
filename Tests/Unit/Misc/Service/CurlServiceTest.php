<?php

namespace BR\Toolkit\Tests\Misc\Service;

use BR\Toolkit\Misc\Native\Curl;
use BR\Toolkit\Misc\Service\CurlService;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class CurlServiceTest extends TestCase
{
    /**
     * @var CurlService
     */
    private $service;
    /**
     * @var MockObject|Curl
     */
    private $curlMock;

    protected function setUp()
    {
        $this->curlMock = $this->getMockBuilder(Curl::class)->getMock();
        $this->service = new CurlService($this->curlMock);
    }

    /**
     * @return array
     */
    public function curlRequestDataProvider(): array
    {
        return [
            ['url', 'GET'],
            ['', 'GET'],
            ['url', 'PUT'],
            ['', ''],
            ['https://test.test/', 'POST'],
            ['https://test.test', 'ABC'],
        ];
    }

    /**
     * @dataProvider curlRequestDataProvider
     * @param string $url
     * @param string $method
     */
    public function testCreateCurlRequestSample(string $url, string $method)
    {
        $request = $this->service->getCurlRequest($url, $method);
        $this->assertEquals($url, $request->getUrl());
        $this->assertEquals($method, $request->getMethod());
        $this->assertNotEmpty($request->getOptions());
    }

    /**
     * @dataProvider curlRequestDataProvider
     * @param string $url
     * @param string $method
     */
    public function testSendSuccessCurlRequest(string $url, string $method)
    {
        $this->curlMock->expects($this->once())
            ->method('curlExec')
            ->willReturn($url);

        $this->curlMock->expects($this->once())
            ->method('curlGetInfo')
            ->willReturn([]);

        $this->curlMock->expects($this->once())
            ->method('curlError')
            ->willReturn('error: ' . $url);

        $this->curlMock->expects($this->once())
            ->method('curlErrno')
            ->willReturn(0);

        $this->curlMock->expects($this->exactly(2))
            ->method('curlIsOpen')
            ->willReturnOnConsecutiveCalls(false, true);

        $request = $this->service->getCurlRequest($url, $method);
        $response = $this->service->execute($request);
        $this->service->closeConnection();

        $this->assertSame($url, $response->getData());
        $this->assertSame('error: ' . $url, $response->getError());
    }

    /**
     * @dataProvider curlRequestDataProvider
     * @param string $url
     * @param string $method
     */
    public function testSendSuccessCurlRequestFailed(string $url, string $method)
    {
        $this->curlMock->expects($this->once())
            ->method('curlExec')
            ->willReturn($url);

        $this->curlMock->expects($this->once())
            ->method('curlGetInfo')
            ->willReturn([]);

        $this->curlMock->expects($this->once())
            ->method('curlError')
            ->willReturn('error: ' . $url);

        $this->curlMock->expects($this->once())
            ->method('curlErrno')
            ->willReturn(6);

        $this->curlMock->expects($this->exactly(2))
            ->method('curlIsOpen')
            ->willReturnOnConsecutiveCalls(false, true);

        $request = $this->service->getCurlRequest($url, $method);
        $response = $this->service->execute($request);
        $this->service->closeConnection();

        $this->assertSame($url, $response->getData());
        $this->assertSame('error: ' . $url, $response->getError());
        $this->assertTrue($response->isError());
    }
}