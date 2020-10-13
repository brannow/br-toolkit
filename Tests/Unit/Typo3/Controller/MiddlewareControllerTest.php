<?php

namespace BR\Toolkit\Tests\Typo3\Controller;

use BR\Toolkit\Typo3\Controller\MiddlewareController;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ServerRequestInterface;

class TestController extends MiddlewareController
{
    public function getRequest(): ServerRequestInterface
    {
        return parent::getRequest();
    }
}

class MiddlewareControllerTest extends TestCase
{
    public function testRequestInject()
    {
        $controller = new TestController();
        /** @var ServerRequestInterface $request */
        $request = $this->getMockBuilder(ServerRequestInterface::class)->getMockForAbstractClass();
        $controller->setRequest($request);

        $this->assertSame($controller->getRequest(), $request);

    }
}