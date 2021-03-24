# TYPO3 / Cache / CacheService

configuration free, out of the box working, symfony like, TYPO3 based caching.

for a better I/O performance, every cache entry is organized in a "CacheBag".
that means cache entries are grouped loaded and stored together based on the context name.

cacheBags are stored in Memory (for runtime), and only loaded once.
cacheWrite happend only if something is updated.

```php
// keyA and keyB are stored in the same cacheGroup
// that means keyA and KeyB are loaded at once if contextA is requested

// first cache request for contextA entire cacheBag (contextA) is loaded from cacheStoreage
$this->cacheService->cache('keyA', func..., 'contextA', 1000);
// contextA cacheBag is already in storage so no cacheLoading needed, value for keyB is already in memory
$this->cacheService->cache('keyB', func..., 'contextA', 1000);

// new cacheContext is requested... etc...
$this->cacheService->cache('keyA', func..., 'contextB', 1000);
```

##### Limitations
currently only scalar values are cacheable, or everything that can be `serialize` / `unserialize`

#### Interfaces

* `BR\Toolkit\Typo3\Cache\CacheServiceInterface`

--- 

#### CacheServiceInterface

Cache service for any autowired scope
```php
use BR\Toolkit\Typo3\Cache\CacheServiceInterface;

class ExampleClass
{
    private CacheServiceInterface $cacheService;

    public function __construct(CacheServiceInterface $cacheService) 
    {
        $this->cacheService = $cacheService;
    }

    public function exampleAction(): string 
    {
        $outerVar = 'Hello';
    
        return $this->cacheService->cache(
            'cacheIdentifierKey', // cache key
            function () use ($outerVar) {
                // this will be only executed if there is no caching entry found for the CacheKey + Context
                return $outerVar . ' World';
                
            },
            'CustomContext', // cache Context
            100 // time to live in seconds - 0 or negative values are unlimited - null is default ttl of 3600
        );
    }
    
    // alternative method to get the cacheService
    public static function nonDependencyInjectedMethod(): string
    {
        // use the InstanceUtility to DependencyInject at runtime (not recommended)
        $cacheService = \BR\Toolkit\Typo3\VersionWrapper\InstanceUtility::get(CacheServiceInterface::class);
        // ...
        
        return '';
    }
}
```


---

##### Methods

* [cache](#cache)
* [destroy](#destroy)

#### cache
Cache method
```php
public function cache(string $key, callable $block, string $context = CacheServiceInterface::CONTEXT_GLOBAL, int $ttl = null);
```

##### Arguments
* `string $key` cache key identiier
* `callable $block` execute block if no cache entry is found based on cacheKey and Context
* `string $context` cacheContext
* `int|null $ttl`  time to live in seconds - <= 0 means unlimited, null is default

NOTE: ttl is handled by the cacheService itself, not managed by the native typo3 cache interface, this is default 0 (unlimited),

##### Return
 * mixed scalar value
 

#### destroy
force destory a cache entry manually 
```php
public function destroy(string $key, string $context = CacheServiceInterface::CONTEXT_GLOBAL): bool
```

##### Arguments
* `string $key` cache key identifier
* `string $context` cacheContext

##### Return
* `bool` true if cache entry is deleted / false if no cache entry was found 