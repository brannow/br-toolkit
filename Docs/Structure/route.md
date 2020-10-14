# TYPO3 / Route

Middleware Routing Configuration (see: `BR\Toolkit\Typo3\Middleware\RequestRoutingMiddleware`).    
Used in `proteced function getRouting(): array;`

## Classes

* `BR\Toolkit\Typo3\DTO\Route`

### Route

* [createRoute](#createroute)

#### createRoute
creates a route,
* `$controller` must be a fully className string    
* `$action` visible method name (controller and action must be callable)
* `$uri` the uri listener (uri path)
* `$method` allowed methods, empty means everything allowed (see: https://developer.mozilla.org/de/docs/Web/HTTP/Methods)

```php
public static function createRoute(
    string $controller, 
    string $action, 
    string $uri, 
    array $method = []
): RouteInterface
```

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
checked if the current route is compatible with the `ServerRequestInterface $request`.     
The Match depends on the request URI and HTTP_METHOD
this method is, by default, caseINsensitive!
```php
public function match(
    ServerRequestInterface $request
): bool
```
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
get a `callable` from the configurated class + method. if configuration is is not "callable" like class did not exists or method is protected or private, `RoutingException` is thrown.
```php
public function getControllerCallable(): callable
```

##### Example
```php
$route = Route::createRoute(TestController::class, 'someAction', '/api/something', ['GET', 'POST']);
$controllerMethodResult = ($route->getControllerCallable)();
```