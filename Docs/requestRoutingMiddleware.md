# TYPO3 / Middleware / RequestRoutingMiddleware

see [TYPO3 Docs](https://docs.typo3.org/m/typo3/reference-coreapi/master/en-us/ApiOverview/RequestHandling/Index.html) 
for more information about Middlewares.

Catch Request with an buildIn Controller/Route matching system. Especially useful for example api implementations.

Create a own Middleware who is a subclass of `RequestRoutingMiddleware`

See: [Route Docs](/Docs/Structure/route.md) for more information

##### Methods

* [getRouting](#getrouting)

#### getRouting
overwrite this method in your Middleware-Subclass
```php
protected function getRouting(): array;
```

##### Return
 * [`\BR\Toolkit\Typo3\DTO\RouteInterface[]`](/Docs/Structure/route.md)
 
##### example
```php
class AjaxMiddleware extends \BR\Toolkit\Typo3\Middleware\RequestRoutingMiddleware
{
    protected function getRouting(): array
    {
        return [
            Route::createRoute(AjaxController::class, 'mainAction', '/api/member/login', ['GET', 'POST'])
        ];
    }
}
```