<?php

namespace BR\Toolkit\Tests\Typo3\Middleware;

use BR\Toolkit\Exceptions\RoutingException;
use BR\Toolkit\Typo3\Controller\JsonAwareControllerInterface;
use BR\Toolkit\Typo3\Controller\MiddlewareController;
use BR\Toolkit\Typo3\DTO\Route;
use BR\Toolkit\Typo3\Middleware\RequestRoutingMiddleware;
use PHPUnit\Framework\Exception;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\UriInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Throwable;
use TYPO3\CMS\Core\Http\HtmlResponse;
use TYPO3\CMS\Core\Http\JsonResponse;
use TYPO3\CMS\Core\Http\NullResponse;
use TYPO3\CMS\Core\Http\Response;
use TYPO3\CMS\Core\Http\Stream;

class ControllerStub extends MiddlewareController implements JsonAwareControllerInterface
{
    public static $response = null;

    public static $throwException = false;

    public function existsAction() {

        if (self::$throwException) {
            throw new Exception();
        }

        return static::$response;
    }
}

class MiddlewareSampleStub extends RequestRoutingMiddleware
{
    private $routing = [];

    /**
     * @param array $routing
     */
    public function setRouting(array $routing)
    {
        $this->routing = $routing;
    }

    protected function getRouting(): array
    {
        return $this->routing;
    }
}

class MiddlewareSampleStubHandleExceptionEnabled extends MiddlewareSampleStub
{
    protected function canHandleException(Throwable $exception): bool
    {
        return true;
    }
}

class MiddlewareStub extends MiddlewareSampleStub
{
    private $routing = [];

    public static $canHandleException = false;
    public static $exceptionOutput = ['test' => 'case'];

    /**
     * @param array $routing
     */
    public function setRouting(array $routing)
    {
        $this->routing = $routing;
    }

    protected function getRouting(): array
    {
        return $this->routing;
    }

    protected function canHandleException(Throwable $exception): bool
    {
        return self::$canHandleException;
    }

    protected function handleException(Throwable $exception)
    {
        return new JsonResponse(self::$exceptionOutput);
    }
}

class RequestRoutingMiddlewareTest extends TestCase
{
    /**
     * @var MockObject|ServerRequestInterface
     */
    private $request;
    /**
     * @var MockObject|UriInterface
     */
    private $uri;
    /**
     * @var MockObject|RequestHandlerInterface
     */
    private $handler;

    /**
     * @var RequestRoutingMiddleware
     */
    private $middleware;

    protected function setUp(): void
    {
        MiddlewareStub::$canHandleException = false;
        ControllerStub::$throwException = false;
        ControllerStub::$response = null;
        $this->uri = $this->getMockBuilder(UriInterface::class)->getMock();
        $this->uri->expects($this->any())
            ->method('getPath')
            ->willReturn('/test/exists');
        $this->request = $this->getMockBuilder(ServerRequestInterface::class)->getMock();
        $this->request->expects($this->any())
            ->method('getUri')
            ->willReturn($this->uri);
        $this->request->expects($this->any())
            ->method('getMethod')
            ->willReturn('GET');
        $this->handler = $this->getMockBuilder(RequestHandlerInterface::class)->getMock();
        $this->middleware = new MiddlewareStub();
    }

    /**
     * @throws Throwable
     */
    public function testNoRoute()
    {
        MiddlewareStub::$canHandleException = false;
        ControllerStub::$throwException = false;
        $this->handler->expects($this->once())
            ->method('handle');
        $this->middleware->process($this->request, $this->handler);
    }

    /**
     * @return array|array[]
     */
    public static function routeSuccessProvider(): array
    {
        return [
            [
                ControllerStub::class,
                'existsAction',
                '/test/exists',
                [],
                'stringData',
                HtmlResponse::class
            ],
            [
                ControllerStub::class,
                'existsAction',
                '/test/exists',
                [],
                new HtmlResponse(''),
                HtmlResponse::class
            ],
            [
                ControllerStub::class,
                'existsAction',
                '/test/exists',
                [],
                null,
                NullResponse::class
            ],
            [
                ControllerStub::class,
                'existsAction',
                '/test/exists',
                [],
                '',
                NullResponse::class
            ],
            [
                ControllerStub::class,
                'existsAction',
                '/test/exists',
                [],
                new Stream('php://temp', 'r+'),
                Response::class
            ],
            [
                ControllerStub::class,
                'existsAction',
                '/test/exists',
                [],
                1,
                RoutingException::class
            ],
            [
                ControllerStub::class,
                'existsAction',
                '/test/exists',
                [],
                ['test' => 'json'],
                JsonResponse::class
            ],
        ];
    }

    /**
     * @dataProvider routeSuccessProvider
     * @param string $class
     * @param string $action
     * @param string $uri
     * @param array $methods
     * @param $response
     * @param string $responseClassExpectation
     * @throws Throwable
     */
    public function testRouteSuccess(string $class, string $action, string $uri, array $methods, $response, string $responseClassExpectation)
    {
        MiddlewareStub::$canHandleException = false;
        ControllerStub::$throwException = false;
        if ($responseClassExpectation === RoutingException::class) {
            $this->expectException($responseClassExpectation);
        }

        ControllerStub::$response = $response;
        $route = Route::createRoute($class, $action, $uri, $methods);
        $this->middleware->setRouting([$route]);
        $result = $this->middleware->process($this->request, $this->handler);
        $this->assertInstanceOf($responseClassExpectation, $result);
    }

    /**
     * @return array|array[]
     */
    public static function routeFailRoutingProvider(): array
    {
        return [
            [
                ControllerStub::class,
                'existsAction',
                '/test/missing',
                ['GET']
            ],
            [
                ControllerStub::class,
                'existsAction',
                '/test/exists',
                ['POST']
            ]
        ];
    }

    /**
     * @dataProvider routeFailRoutingProvider
     * @param string $class
     * @param string $action
     * @param string $uri
     * @param array $methods
     * @throws Throwable
     */
    public function testRoutingFailure(string $class, string $action, string $uri, array $methods)
    {
        MiddlewareStub::$canHandleException = false;
        ControllerStub::$throwException = false;
        $this->handler->expects($this->once())
            ->method('handle');
        $route = Route::createRoute($class, $action, $uri, $methods);
        $this->middleware->setRouting([$route]);
        $this->middleware->process($this->request, $this->handler);
    }

    public static function routeFailSetupProvider(): array
    {
        return [
            [
                'randomStuff',
                'existsAction',
                '/test/exists',
                ['GET']
            ],
            [
                ControllerStub::class,
                'missingAction',
                '/test/exists',
                ['GET']
            ]
        ];
    }

    /**
     * @dataProvider routeFailSetupProvider
     * @param string $class
     * @param string $action
     * @param string $uri
     * @param array $methods
     * @throws Throwable
     */
    public function testRoutingSetupFailure(string $class, string $action, string $uri, array $methods)
    {
        MiddlewareStub::$canHandleException = false;
        ControllerStub::$throwException = false;
        $this->expectException(RoutingException::class);
        $route = Route::createRoute($class, $action, $uri, $methods);
        $this->middleware->setRouting([$route]);
        $this->middleware->process($this->request, $this->handler);
    }

    /**
     * @throws Throwable
     */
    public function testExceptionHandling()
    {
        MiddlewareStub::$canHandleException = true;
        MiddlewareStub::$exceptionOutput = ['test' => 'case'];
        ControllerStub::$throwException = true;
        $route = Route::createRoute(ControllerStub::class, 'existsAction', '/test/exists', []);
        $this->middleware->setRouting([$route]);
        $jsonResult = $this->middleware->process($this->request, $this->handler);
        $this->assertInstanceOf(JsonResponse::class, $jsonResult);
        $data = json_decode($jsonResult->getBody()->getContents(), true);
        $this->assertSame($data, MiddlewareStub::$exceptionOutput);
    }

    /**
     * @throws Throwable
     */
    public function testExceptionHandlingExclusiveNotHandled()
    {
        $this->expectException(Exception::class);
        MiddlewareStub::$canHandleException = false;
        ControllerStub::$throwException = true;
        $route = Route::createRoute(ControllerStub::class, 'existsAction', '/test/exists', []);
        $this->middleware->setRouting([$route]);
        $this->middleware->process($this->request, $this->handler);
    }

    /**
     * @throws Throwable
     */
    public function testExceptionHandlingDefaultSetup()
    {
        $this->expectException(Exception::class);
        $middleware = new MiddlewareSampleStub();
        ControllerStub::$throwException = true;
        $route = Route::createRoute(ControllerStub::class, 'existsAction', '/test/exists', []);
        $middleware->setRouting([$route]);
        $middleware->process($this->request, $this->handler);
    }

    /**
     * @throws Throwable
     */
    public function testExceptionHandlingDefaultSetupButHandleException()
    {
        $middleware = new MiddlewareSampleStubHandleExceptionEnabled();
        ControllerStub::$throwException = true;
        $route = Route::createRoute(ControllerStub::class, 'existsAction', '/test/exists', []);
        $middleware->setRouting([$route]);
        $nullResponse = $middleware->process($this->request, $this->handler);
        $this->assertInstanceOf(NullResponse::class, $nullResponse);
    }
}