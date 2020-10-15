# TYPO3 / Route

Middleware Routing Configuration (see: `BR\Toolkit\Typo3\Middleware\RequestRoutingMiddleware`).    
Used in `proteced function getRouting(): array;`

## Classes

* `BR\Toolkit\Typo3\DTO\Route`

### Route

* [createRoute](#createroute)

#### createRoute
creates a route,


```php
public static function createRoute(
    string $controller, 
    string $action, 
    string $uri, 
    array $method = []
): RouteInterface
```

##### Arguments
* `string $controller` must be a fully className string    
* `string $action` visible method name (controller and action must be callable)
* `string $uri` the uri listener (uri path)
* `string[] $method` allowed methods, empty means everything allowed (see: https://developer.mozilla.org/de/docs/Web/HTTP/Methods)
 
##### Return
 * `BR\Toolkit\Typo3\DTO\RouteInterface`

##### example
```php
// /api/something - GET
// /api/something - POST
// ...
$route = Route::createRoute(TestController::class, 'someAction', '/api/something', []);
```

```php
// only: /api/something - GET
$route = Route::createRoute(TestController::class, 'someAction', '/api/something', ['GET']);
```

## Interfaces

* `BR\Toolkit\Typo3\DTO\RouteInterface`
* `BR\Toolkit\Typo3\DTO\RequestInjectInterface`

### RouteInterface

* [match](#match)
* [getControllerCallable](#getcontrollercallable)

#### match
checked if the current route is compatible with the current `$request`.
```php
public function match(
    ServerRequestInterface $request
): bool
```

##### Arguments
* `Psr\Http\Message\ServerRequestInterface $request` requestObject

##### Return
 * `bool`

##### example
```php
// $request = ServerRequestInterface URI = /api/something - GET
$route = Route::createRoute(TestController::class, 'someAction', '/api/something', []);
$result = $route->match($request);
// $result = true
```

```php
// $request = ServerRequestInterface URI = /api/something - PUT
$route = Route::createRoute(TestController::class, 'someAction', '/api/something', ['GET', 'POST']);
$result = $route->match($request);
// $result = false
```

```php
// $request = ServerRequestInterface URI = /api/anything - GET
$route = Route::createRoute(TestController::class, 'someAction', '/api/something', []);
$result = $route->match($request);
// $result = false
```

#### getControllerCallable
get a `callable` from the configurated class + method.
```php
public function getControllerCallable(): callable
```

##### Return
 * `callable`


##### Example
```php
$route = Route::createRoute(TestController::class, 'someAction', '/api/something', ['GET', 'POST']);
$controllerMethodResult = ($route->getControllerCallable)();
// this executes TestController::someAction()
```