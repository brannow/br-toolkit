<?php

namespace BR\Toolkit\Typo3\Middleware;

use BR\Toolkit\Exceptions\RoutingException;
use BR\Toolkit\Typo3\DTO\RequestInjectInterface;
use BR\Toolkit\Typo3\DTO\RouteInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use TYPO3\CMS\Core\Http\HtmlResponse;
use TYPO3\CMS\Core\Http\NullResponse;
use TYPO3\CMS\Core\Http\Response;

abstract class RequestRoutingMiddleware implements MiddlewareInterface
{
    /**
     * @return RouteInterface[]
     */
    abstract protected function getRouting(): array;

    /**
     * @param ServerRequestInterface $request
     * @param RequestHandlerInterface $handler
     * @return ResponseInterface
     * @throws RoutingException
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {

        $route = $this->processRouting($request);
        if ($route) {
            $data = $this->processController($route);
            return $this->processResponse($data);
        }

        return $handler->handle($request);
    }

    /**
     * @param ServerRequestInterface $request
     * @return RouteInterface|null
     */
    protected function processRouting(ServerRequestInterface $request): ?RouteInterface
    {
        foreach ($this->getRouting() as $route) {
            if ($route->match($request)) {
                if ($route instanceof RequestInjectInterface) {
                    $route->setRequest($request);
                }
                return $route;
            }
        }

        return null;
    }

    /**
     * @param RouteInterface $route
     * @return mixed
     * @throws RoutingException
     */
    protected function processController(RouteInterface $route)
    {
        return ($route->getControllerCallable())();
    }

    /**
     * @param ResponseInterface|null|string|StreamInterface $body
     * @return ResponseInterface
     * @throws RoutingException
     */
    protected function processResponse($body): ResponseInterface
    {
        if ($body instanceof ResponseInterface) {
            return $body;
        } elseif ($body === null || $body === '') {
            return new NullResponse();
        } elseif (is_string($body)) {
            return new HtmlResponse($body);
        } elseif ($body instanceof StreamInterface) {
            return new Response($body);
        }

        throw new RoutingException('unexpected controller response', 1000);
    }
}