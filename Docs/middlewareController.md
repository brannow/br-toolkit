# TYPO3 / Controller / MiddlewareController

use this as Foundation for every Middleware Controller that will be used in [`RequesrRoutingMiddleware:getRouting()`](/Docs/requestRoutingMiddleware.md)

#### Interfaces

* `\BR\Toolkit\Typo3\Controller\MiddlewareControllerInterface`
* `\BR\Toolkit\Typo3\DTO\RequestInjectInterface`

#### RequestInjectInterface

Allows the Middleware to inject the current request into the controller

##### Methods

* [getRequest](#getrequest)
* [setRequest](#setrequest)

#### getRequest
get the current [`\Psr\Http\Message\ServerRequestInterface`](https://www.php-fig.org/psr/psr-7/)
```php
protected function getRouting(): array
```

##### Return
 * [`\BR\Toolkit\Typo3\DTO\RouteInterface[]`](/Docs/Structure/route.md)
 

#### setRequest
set the current [`\Psr\Http\Message\ServerRequestInterface`](https://www.php-fig.org/psr/psr-7/)
```php
protected function setRouting(ServerRequestInterface $request): void
```

##### Arguments
 * [`\Psr\Http\Message\ServerRequestInterface $request`](https://www.php-fig.org/psr/psr-7/)
 