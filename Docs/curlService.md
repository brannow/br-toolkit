# Misc / Service / CurlService

Use the CurlService to send / receive data.    
Subclass it to create for example an API Service

##### Methods

* [getCurlRequest](#getcurlrequest)
* [execute](#execute)
* [closeConnection](#closeconnection)

#### getCurlRequest
create a basic preset [`CurlRequestInterface`](/Docs/Structure/curl.md) object

```php
public function getCurlRequest(string $url = '', string $method = 'GET'): CurlRequestInterface
```

##### Arguments
 * `string $url` web url
 * `string $method` HTTP method (see: https://www.w3.org/Protocols/rfc2616/rfc2616-sec9.html)

##### Return
 * [`\BR\Toolkit\Misc\DTO\Curl\CurlRequestInterface`](/Docs/Structure/curl.md)

##### example
```php
// use dependency injection for the service
$curlService = new \BR\Toolkit\Misc\Service\CurlService(new \BR\Toolkit\Misc\Native\Curl());
$request = $curlService->getCurlRequest('https://github.com', 'POST');
```

---

#### execute
sends the request and received the response data/error data.

```php
public function execute(CurlRequestInterface $curlRequest): CurlResponseInterface
```

##### Arguments
 * [`CurlRequestInterface $curlRequest`](/Docs/Structure/curl.md)

##### Return
 * [`\BR\Toolkit\Misc\DTO\Curl\CurlResponseInterface`](/Docs/Structure/curl.md)

##### example
```php
// use dependency injection for the service
$curlService = new \BR\Toolkit\Misc\Service\CurlService(new \BR\Toolkit\Misc\Native\Curl());
$request = $curlService->getCurlRequest('https://github.com', 'POST');
$response = $curlService->execute($request);

if ($response->isError()) {
    echo $response->getError();
} else {
    echo $response->getData();
}
```

---

#### closeConnection
close the used connection, otherwise curlService stays "open" for reuse.   
closed connections will automatically re-opened if another execute is fired.

Connection Closed is not strictly mandatory, but bear in mind there is a limit of open connections per system ;) 
```php
public function closeConnection(): void
```

##### example
```php
$curlService = new \BR\Toolkit\Misc\Service\CurlService(new \BR\Toolkit\Misc\Native\Curl());
$request = $curlService->getCurlRequest('https://github.com', 'POST');
$response = $curlService->execute($request);

$curlService->closeConnection();
```