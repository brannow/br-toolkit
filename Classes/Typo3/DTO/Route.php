<?php

namespace BR\Toolkit\Typo3\DTO;

use BR\Toolkit\Exceptions\RoutingException;
use BR\Toolkit\Typo3\Controller\MiddlewareControllerInterface;
use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Object\ObjectManager;
use TYPO3\CMS\Extbase\Object\ObjectManagerInterface;

class Route implements RouteInterface, RequestInjectInterface
{
    /**
     * @var string
     */
    private $controller;
    /**
     * @var string
     */
    private $action;
    /**
     * @var string
     */
    private $uri;
    /**
     * @var array
     */
    private $method;

    /**
     * @var ServerRequestInterface|null
     */
    private $request = null;

    /**
     * Route constructor.
     * @param string $controller
     * @param string $action
     * @param string $uri
     * @param array $method
     */
    public function __construct(string $controller, string $action, string $uri, array $method = [])
    {
        $this->controller = $controller;
        $this->action = $action;
        $this->uri = $uri;
        $this->method = array_map('strtoupper', $method);
    }

    /**
     * @param string $controller
     * @param string $action
     * @param string $uri
     * @param array $method
     * @return RouteInterface
     */
    public static function createRoute(string $controller, string $action, string $uri, array $method = []): RouteInterface
    {
        return new static($controller, $action, $uri, $method);
    }

    /**
     * @param ServerRequestInterface $request
     */
    public function setRequest(ServerRequestInterface $request): void
    {
        $this->request = $request;
    }

    /**
     * @param ServerRequestInterface $request
     * @return bool
     */
    public function match(ServerRequestInterface $request): bool
    {
        if ($request->getUri() === null || strtolower($request->getUri()->getPath()) !== strtolower($this->uri)) {
            return false;
        }

        if (!(empty($this->method) || in_array(strtoupper($request->getMethod()), $this->method))) {
            return false;
        }

        return true;
    }

    /**
     * @return callable
     * @throws RoutingException
     */
    public function getControllerCallable(): callable
    {
        $controllerInstance = $this->getControllerInstance();
        $callable = [$controllerInstance, $this->action];
        if (!is_callable($callable)) {
            throw new RoutingException('controller method \''. $this->controller .'\' not callable in \' '. $this->action .'\' ', 1001);
        }

        return $callable;
    }

    /**
     * @return RequestInjectInterface|object
     * @throws RoutingException
     */
    private function getControllerInstance()
    {
        if (!class_exists($this->controller)) {
            throw new RoutingException('class \''. $this->controller .'\' not exists');
        }
        /** @var ObjectManagerInterface $om */
        $om = GeneralUtility::makeInstance(ObjectManager::class);
        /** @var MiddlewareControllerInterface processingController */
        $controllerInstance = $om->get($this->controller);
        if ($controllerInstance instanceof RequestInjectInterface) {
            $controllerInstance->setRequest($this->request);
        }
        return $controllerInstance;
    }
}