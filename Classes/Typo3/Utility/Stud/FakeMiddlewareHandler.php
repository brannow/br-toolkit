<?php

namespace BR\Toolkit\Typo3\Utility\Stud;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use TYPO3\CMS\Core\Http\NullResponse;
use TYPO3\CMS\Core\SingletonInterface;

class FakeMiddlewareHandler implements RequestHandlerInterface, SingletonInterface
{
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        return new NullResponse();
    }
}