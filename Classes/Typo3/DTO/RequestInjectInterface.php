<?php


namespace BR\Toolkit\Typo3\DTO;


use Psr\Http\Message\ServerRequestInterface;

interface RequestInjectInterface
{
    /**
     * @param ServerRequestInterface $request
     */
    public function setRequest(ServerRequestInterface $request): void;
}