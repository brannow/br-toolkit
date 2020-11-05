# TYPO3 / Controller / MiddlewareController

use this as Foundation for every Middleware Controller that will be used in [`RequesrRoutingMiddleware:getRouting()`](/Docs/requestRoutingMiddleware.md)

the middleware controller is called by the middleware, not the middleware itself.   
see: [`RequesrRoutingMiddleware`](/Docs/requestRoutingMiddleware.md) as the actual middleware processor

#### Interfaces

* `\BR\Toolkit\Typo3\Controller\MiddlewareControllerInterface`
* `\BR\Toolkit\Typo3\Controller\JsonAwareControllerInterface`
* `\BR\Toolkit\Typo3\DTO\RequestInjectInterface`

--- 

#### JsonAwareControllerInterface

Add this to your Controller 
```php
class ExampleAjaxController extends MiddlewareController implements JsonAwareControllerInterface
{
    public function exampleAction(): array 
    {
        return ['hello' => 'world'];
    }
}
```

the middleware will check if the action return value is an array AND if the `JsonAwareControllerInterface` is implemented.
if both present the output will be a json_response

---

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
 