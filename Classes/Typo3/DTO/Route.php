<?php

namespace BR\Toolkit\Typo3\DTO;

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
     * @return Route
     */
    public static function createRoute(string $controller, string $action, string $uri, array $method = []): Route
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
        if (stripos($request->getUri()->getPath(), $this->uri) !== 0) {
            return false;
        }

        if (!(empty($this->method) || in_array(strtoupper($request->getMethod()), $this->method))) {
            return false;
        }

        return true;
    }

    /**
     * @return callable
     */
    public function getControllerCallable(): callable
    {
        /** @var ObjectManagerInterface $om */
        $om = GeneralUtility::makeInstance(ObjectManager::class);
        /** @var MiddlewareControllerInterface processingController */
        $controllerInstance = $om->get($this->controller);
        if ($controllerInstance instanceof RequestInjectInterface) {
            $controllerInstance->setRequest($this->request);
        }
        return [$controllerInstance, $this->action];
    }
}