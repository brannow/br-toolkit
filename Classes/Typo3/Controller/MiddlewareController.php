<?php
namespace BR\Toolkit\Typo3\Controller;

use BR\Toolkit\Typo3\DTO\RequestInjectInterface;
use Psr\Http\Message\ServerRequestInterface;

abstract class MiddlewareController implements MiddlewareControllerInterface, RequestInjectInterface
{
    /**
     * @var ServerRequestInterface
     */
    private $request;

    /**
     * this is called from the Middleware itself
     * @param ServerRequestInterface $request
     */
    public function setRequest(ServerRequestInterface $request): void
    {
        $this->request = $request;
    }
}