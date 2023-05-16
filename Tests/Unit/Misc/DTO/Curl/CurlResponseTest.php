<?php

namespace BR\Toolkit\Tests\Misc\DTO\Curl;

use BR\Toolkit\Misc\DTO\Curl\CurlResponse;
use PHPUnit\Framework\TestCase;

class CurlResponseTest extends TestCase
{
    /**
     * @return array
     */
    public static function responseProvider(): array
    {
        return [
            // data, headers, errorStr, errorNr, data_as_array
            ['', [], '', 0, []],
            ['', [], '', 123, []],
            [json_encode(['name' => 'test']), [], '', 0, ['name' => 'test']],
            ['a', ['a' => 'b', 'c'], '-', -1, []],
        ];
    }

    /**
     * @dataProvider responseProvider
     * @param string $data
     * @param array $headers
     * @param string $error
     * @param int $errno
     * @param array $dataAsArray
     */
    public function testCurlResponse(string $data, array $headers, string $error, int $errno, array $dataAsArray)
    {
        $response = new CurlResponse($data, $headers, $error, $errno);

        $this->assertEquals($error, $response->getError());
        $this->assertIsArray($response->getArrayDataFromJson());
        $this->assertSame($errno !== 0, $response->isError());
        $this->assertSame($dataAsArray, $response->getArrayDataFromJson());
    }
}