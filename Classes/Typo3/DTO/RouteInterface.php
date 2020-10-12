<?php


namespace BR\Toolkit\Typo3\DTO;


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
     */
    public function getControllerCallable(): callable;
}