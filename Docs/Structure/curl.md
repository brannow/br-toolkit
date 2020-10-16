# Misc / Curl

Curl Request/Response Data Abstraction (used in: `BR\Toolkit\Misc\Service\CurlService`)

## Classes

* `BR\Toolkit\Typo3\DTO\Curl\CurlRequest`
* `BR\Toolkit\Typo3\DTO\Curl\CurlRequestData`
* `BR\Toolkit\Typo3\DTO\Curl\CurlRequestOptions`
* `BR\Toolkit\Typo3\DTO\Curl\CurlResponse`

## Interfaces

* [`BR\Toolkit\Typo3\DTO\Curl\CurlRequestInterface`](#curlrequestinterface)
* [`BR\Toolkit\Typo3\DTO\Curl\CurlRequestDataInterface`](#curlrequestdatainterface)
* [`BR\Toolkit\Typo3\DTO\Curl\CurlRequestOptionsInterface`](#curlrequestoptionsinterface)
* [`BR\Toolkit\Typo3\DTO\Curl\CurlResponseInterface`](#curlresponseinterface)

### CurlRequestInterface

##### extends
* `BR\Toolkit\Typo3\DTO\Curl\CurlRequestDataInterface`
* `BR\Toolkit\Typo3\DTO\Curl\CurlRequestOptionsInterface`


### CurlRequestDataInterface

Configuration for all CurlRequest Data that involves all transfered data between server / client

* [getUrl](#geturl)
* [setUrl](#seturl)
* [setMethod](#setmethod)
* [isPost](#ispost)
* [setQuery](#setquery)
* [getQuery](#getquery)
* [getQueryString](#getquerystring)
* [setData](#setdata)
* [getData](#getdata)
* [getDataString](#getdatastring)

#### getUrl
get the full url (this includes also `query data` as well)
```php
public function getUrl(): string;
```
##### Return
* `string`

##### Example
```php
$request = new \BR\Toolkit\Misc\DTO\Curl\CurlRequest();
$request->setUrl('https://example?test=preset')
    ->setQuery('key', 'value');

$url = $request->getUrl();
// $url == https://example?test=preset&key=value
```

---

#### setUrl
sets the curl target url, this can include preset queries.
```php
public function setUrl(string $url): CurlRequestDataInterface;
```
##### Arguments
* `string $url` curl target url

##### Return
* `BR\Toolkit\Typo3\DTO\Curl\CurlRequestDataInterface`

##### Example
```php
$request = new \BR\Toolkit\Misc\DTO\Curl\CurlRequest();
$request->setUrl('https://example');
$url = $request->getUrl();
// $url == https://example
```

---

#### setMethod
Set the Request HTTP_Method
```php
public function setMethod(string $method): CurlRequestDataInterface;
```

##### Arguments
* `string $method` curl HTTP method (see: https://developer.mozilla.org/de/docs/Web/HTTP/Methods)

##### Return
* `BR\Toolkit\Typo3\DTO\Curl\CurlRequestDataInterface`

##### Example
```php
$request = new \BR\Toolkit\Misc\DTO\Curl\CurlRequest();
$method = $request->getMethod();
// $method == GET
$request->setMethod('POST');
$method = $request->getMethod();
// $method == POST
```

---

#### getMethod
Get the current Request HTTP_Method, default is `GET`
```php
public function getMethod(): string;
```

##### Return
* `string`

##### Example
```php
$request = new \BR\Toolkit\Misc\DTO\Curl\CurlRequest();
$method = $request->getMethod();
// $method == GET
```

---

#### isPost
Check if this reuqest a post request, (check respects only the `Method`)
```php
public function isPost(): bool;
```

##### Return
* `bool`

##### Example
```php
$request = new \BR\Toolkit\Misc\DTO\Curl\CurlRequest();
$isPost = $request->isPost();
// $isPost == FALSE
$isPost = $request->setMethod('POST')->isPost();
// $isPost == TRUE
```

---

#### setQuery
Add query data
```php
public function setQuery(string $key, string $value): CurlRequestDataInterface;
```

##### Arguments
* `string $key` url query key (unencoded)
* `string $value` url query value (unencoded)

##### Return
* `BR\Toolkit\Typo3\DTO\Curl\CurlRequestDataInterface`

##### Example
```php
$request = new \BR\Toolkit\Misc\DTO\Curl\CurlRequest();
$request->setUrl('https://example?test=preset')
    ->setQuery('A', 'B');

$url = $request->getUrl();
$query = $request->getQuery();
// $url == https://example?test=preset&A=B
// $query == ['A' => 'B']
```

---

#### getQuery
Get all given query data, this ignores any queryData directly set via `setUrl(string $url)`
```php
public function getQuery(): array;
```

##### Return
* `string[]`

##### Example
```php
$request = new \BR\Toolkit\Misc\DTO\Curl\CurlRequest();
$request->setUrl('https://example?test=preset')
    ->setQuery('A', 'B');

$url = $request->getUrl();
$query = $request->getQuery();
// $url == https://example?test=preset&A=B
// $query == ['A' => 'B']
```

---

#### getQueryString
Generate URL-encoded query string for the given query data
```php
public function getQueryString(): string;
```

##### Return
* `string`

##### Example
```php
$request = new \BR\Toolkit\Misc\DTO\Curl\CurlRequest();
$request->setQuery('A', 'B')
    ->setQuery('C', 'D');

$queryString = $request->getQueryString();
// $queryString == 'A=B&C=D'
```

---

#### setData
Add post data
```php
public function setData(string $key, string $value): CurlRequestDataInterface;
```

##### Arguments
* `string $key` url post data key (unencoded)
* `string $value` url post data value (unencoded)

##### Return
* `BR\Toolkit\Typo3\DTO\Curl\CurlRequestDataInterface`

##### Example
```php
$request = new \BR\Toolkit\Misc\DTO\Curl\CurlRequest();
$request->setData('A', 'B');

$data = $request->getData();
// $data == ['A' => 'B']
```

---

#### getData
Get all given post data
```php
public function getData(): array;
```

##### Return
* `string[]`

##### Example
```php
$request = new \BR\Toolkit\Misc\DTO\Curl\CurlRequest();
$request->setData('A', 'B');

$data = $request->getData();
// $data == ['A' => 'B']
```

---

#### getDataString
Generate URL-encoded string for the given post data 
```php
public function getDataString(): string;
```

##### Return
* `string`

##### Example
```php
$request = new \BR\Toolkit\Misc\DTO\Curl\CurlRequest();
$request->setData('A', 'B')
    ->setData('C', 'D');

$dataString = $request->getDataString();
// $dataString == 'A=B&C=D'
```

### CurlRequestOptionsInterface

Configuration for the Curl connection itself.

* [getOptions](#getoptions)
* [setOption](#setoption)


#### getOptions
Get the Curl Options in format: `[CURL_OPTION => VALUE]`
```php
public function getOptions(): array;
```

##### Return
* `mixed[]`

##### Example
```php
$request = new \BR\Toolkit\Misc\DTO\Curl\CurlRequest();
$request
    ->setOption(CURLOPT_FOLLOWLOCATION, 5)
    ->setOption(CURLOPT_MAXREDIRS, 5);
$options = $request->getOptions();
// $options = [52 => 5, 68 => 5]
```

---

#### setOption
set a curl Option (see: https://www.php.net/manual/en/function.curl-setopt.php)
```php
public function setOption(int $key, $value): CurlRequestOptionsInterface;
```

##### Return
* `BR\Toolkit\Typo3\DTO\Curl\CurlRequestOptionsInterface`

##### Example
```php
$request = new \BR\Toolkit\Misc\DTO\Curl\CurlRequest();
$request->setOption(CURLOPT_VERBOSE, true);
```
### CurlResponseInterface

Curl Response can be a success or failed request

* [isError](#iserror)
* [getError](#geterror)
* [getArrayDataFromJson](#getarraydatafromjson)
* [getData](#getdata)



#### isError
if the Curl-Request failed return true, otherwise false.
```php
public function isError(): bool;
```

##### Return
* `bool`

##### Example
```php
// $curlService = \BR\Toolkit\Misc\Service\CurlService
$request = $curlService->getCurlRequest('https://example', 'GET');
$response = $curlService->execute($request);

if ($response->isError()) {
    throw new \Exception('curl failed');
}
```

---

#### getError
if the Curl-Request failed return the curl error message, otherwise empty string.
```php
public function getError(): string;
```

##### Return
* `string`

##### Example
```php
// $curlService = \BR\Toolkit\Misc\Service\CurlService
$request = $curlService->getCurlRequest('https://example', 'GET');
$response = $curlService->execute($request);

if ($response->isError()) {
    throw new \Exception('curl failed: '. $response->getError());
}
```

---

#### getArrayDataFromJson
if the Responsed data json based this will decode the data into a assoc array
```php
public function getArrayDataFromJson(): array;
```

##### Return
* `array`

##### Example
```php
// $curlService = \BR\Toolkit\Misc\Service\CurlService
$request = $curlService->getCurlRequest('https://example', 'GET');
$response = $curlService->execute($request);
$arrayResponse = $response->getArrayDataFromJson();
```

---

#### getData
get the Response data as string
```php
public function getData(): string;
```

##### Return
* `string`

##### Example
```php
// $curlService = \BR\Toolkit\Misc\Service\CurlService
$request = $curlService->getCurlRequest('https://example', 'GET');
$response = $curlService->execute($request);
$dataString = $response->getData();
```