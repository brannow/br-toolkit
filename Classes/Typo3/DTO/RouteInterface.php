<?php


namespace BR\Toolkit\Typo3\DTO;


use BR\Toolkit\Exceptions\RoutingException;
use Psr\Http\Message\ServerRequestInterface;

interface RouteInterface
{
    /**
     * @param ServerRequestInterface $request
     * @return bool
     */
    public function match(ServerRequestInterface $request): bool;

    /**
     * @return callable
     * @throws RoutingException
     */
    public function getControllerCallable(): callable;
}