<?php

namespace BR\Toolkit\Typo3\Middleware;

use BR\Toolkit\Exceptions\RoutingException;
use BR\Toolkit\Typo3\Controller\JsonAwareControllerInterface;
use BR\Toolkit\Typo3\DTO\RequestInjectInterface;
use BR\Toolkit\Typo3\DTO\RouteInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use TYPO3\CMS\Core\Http\HtmlResponse;
use TYPO3\CMS\Core\Http\JsonResponse;
use TYPO3\CMS\Core\Http\NullResponse;
use TYPO3\CMS\Core\Http\Response;
use Throwable;

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
     * @throws Throwable
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
     * @throws Throwable
     */
    protected function processController(RouteInterface $route)
    {
        $callable = $route->getControllerCallable();

        // execute Controller action and listen for exceptions
        try {
            $result = ($callable)();
        } catch (Throwable $exception) {
            // check if exception can be handled
            if (!$this->canHandleException($exception)) {
                throw $exception;
            }
            $result = $this->handleException($exception);
        }

        if ($callable[0] instanceof JsonAwareControllerInterface && is_array($result)) {
            return new JsonResponse($result);
        }

        return $result;
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

    /**
     * override to custom handle exceptions
     * @param Throwable $exception
     * @return bool
     */
    protected function canHandleException(Throwable $exception): bool
    {
        return false;
    }

    /**
     * @param Throwable $exception
     * @return NullResponse
     */
    protected function handleException(Throwable $exception)
    {
        return new NullResponse();
    }
}