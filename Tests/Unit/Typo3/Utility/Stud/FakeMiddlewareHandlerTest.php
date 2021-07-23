<?php

namespace BR\Toolkit\Tests\Typo3\Utility\Stud;

use BR\Toolkit\Typo3\Utility\Stud\FakeMiddlewareHandler;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use TYPO3\CMS\Core\Http\NullResponse;


class FakeMiddlewareHandlerTest extends TestCase
{
    public function testNullResponse()
    {
        $request = $this->getMockBuilder(ServerRequestInterface::class)->getMock();
        $obj = new FakeMiddlewareHandler();
        $this->assertTrue($obj instanceof RequestHandlerInterface);
        $this->assertTrue($obj->handle($request) instanceof NullResponse);
    }
}