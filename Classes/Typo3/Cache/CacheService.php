<?php


namespace BR\Toolkit\Typo3\Cache;


use BR\Toolkit\Exceptions\CacheException;
use TYPO3\CMS\Core\Cache\Exception;
use TYPO3\CMS\Core\Cache\Backend\BackendInterface;
use TYPO3\CMS\Core\Cache\Frontend\FrontendInterface;
use TYPO3\CMS\Core\Core\Environment;
use TYPO3\CMS\Core\SingletonInterface;

class CacheService implements CacheServiceInterface, SingletonInterface
{
    private const DEBUG_CACHE_CONTEXT = 'DEBUG_CACHE_KEY_CONTEXT';
    private const DEBUG_CACHE_KEY_LIST = 'debug_cache_keys';

    private const NOT_FOUND_BLOCK = 'NOT_FOUND_TRIGGER_BLOCK_0x011011';

    // in seconds
    protected const DEFAULT_TTL = 3600;

    private const KEY_TTL = 'ttl';
    private const KEY_CONTENT = 'content';
    private const KEY_RAW = 'raw';

    /**
     * @var BackendInterface|null
     */
    private static $cacheInstance = null;

    /**
     * @var array[]
     */
    private static $cacheBag = [];

    /**
     * @var array
     */
    private static $cacheMutationFlag = [];

    /**
     * @return BackendInterface|null
     * @throws Exception\DuplicateIdentifierException
     * @throws Exception\InvalidBackendException
     * @throws Exception\InvalidCacheException
     */
    private function getCacheInstance(): ?BackendInterface
    {
        if (self::$cacheInstance instanceof BackendInterface) {
            return self::$cacheInstance;
        }

        try {
            $frontendCache = (new CacheManager(false))->sideLoadCacheFrontend(CacheManager::CACHE_DOMAIN, CacheManager::announceCache());
        } catch (\Exception $_) {
            return null;
        }

        if ($frontendCache instanceof FrontendInterface) {
            return self::$cacheInstance = $frontendCache->getBackend();
        }

        return null;
    }

    /**
     * @param string $key
     * @param callable $block
     * @param string $context
     * @param int|null $ttl
     * @return array|false|mixed|string
     * @throws CacheException
     */
    public function cache(string $key, callable $block, string $context = CacheServiceInterface::CONTEXT_GLOBAL, int $ttl = null)
    {
        $exists = false;
        $value = $this->get($key, $context, $exists);

        if (!$exists) {
            $value = $block();
            $this->set($key, $value, $context, $ttl);
        }

        return $value;
    }

    /**
     * @param string $key
     * @param string $context
     * @return bool
     * @throws Exception\DuplicateIdentifierException
     * @throws Exception\InvalidBackendException
     * @throws Exception\InvalidCacheException
     */
    public function destroy(string $key, string $context = CacheServiceInterface::CONTEXT_GLOBAL): bool
    {
        $this->initCacheBag($context);
        $stableKey = $this->sanitizeKey($key);
        $data = self::$cacheBag[$context];

        if (!isset($data[$stableKey])) {
            return false;
        }
        unset($data[$stableKey]);
        self::$cacheBag[$context] = $data;
        return $this->storeCacheBag($context);
    }

    /**
     * @param string $key
     * @param string $context
     * @param bool $exists
     * @return array|false|mixed|string
     * @throws Exception\DuplicateIdentifierException
     * @throws Exception\InvalidBackendException
     * @throws Exception\InvalidCacheException
     */
    private function get(string $key, string $context, bool &$exists)
    {
        $exists = true;
        $this->initCacheBag($context);
        $stableKey = $this->sanitizeKey($key);
        $content = self::$cacheBag[$context][$stableKey][self::KEY_CONTENT]??self::NOT_FOUND_BLOCK;
        $ttl = (int)(self::$cacheBag[$context][$stableKey][self::KEY_TTL]??-1);

        if ($ttl > 0 && time() > $ttl) {
            $this->destroy($stableKey, $context);
            $content = self::NOT_FOUND_BLOCK;
        }

        if ($content === self::NOT_FOUND_BLOCK) {
            $exists = false;
            return '';
        }

        if ((self::$cacheBag[$context][$stableKey][self::KEY_RAW]??true)) {
            $crc = crc32($content);
            $content = self::$cacheBag[$context][$stableKey][self::KEY_CONTENT] = unserialize($content);
            self::$cacheBag[$context][$stableKey][self::KEY_RAW] = false;
            if ($content === false || (is_string($content) && crc32($content) === $crc)) {
                $this->destroy($stableKey, $context);
                $content = self::NOT_FOUND_BLOCK;
            }
        }

        return $content;
    }

    /**
     * @param string $key
     * @param $content
     * @param string $context
     * @param int|null $ttl
     * @throws CacheException
     * @throws Exception\DuplicateIdentifierException
     * @throws Exception\InvalidBackendException
     * @throws Exception\InvalidCacheException
     */
    private function set(string $key, $content, string $context, int $ttl = null)
    {
        if ($content === self::NOT_FOUND_BLOCK) {
            throw new CacheException('content cannot be stored, \''. self::NOT_FOUND_BLOCK .'\' is reserved as internal trigger message');
        }

        $this->initCacheBag($context);
        $data = self::$cacheBag[$context];
        $stableKey = $this->sanitizeKey($key);
        $data[$stableKey][self::KEY_CONTENT] = $content;
        $data[$stableKey][self::KEY_TTL] = $this->getExpireTime($ttl);
        $data[$stableKey][self::KEY_RAW] = false;
        self::$cacheBag[$context] = $data;
        $this->storeCacheBag($context);
    }

    /**
     * @param string $context
     * @return bool
     * @throws Exception\DuplicateIdentifierException
     * @throws Exception\InvalidBackendException
     * @throws Exception\InvalidCacheException
     */
    private function storeCacheBag(string $context): bool
    {
        $this->initCacheBag($context);
        $data = self::$cacheBag[$context];
        $instance = $this->getCacheInstance();
        if ($instance !== null) {
            $dataString = $this->serializeData($data);
            $checksum = crc32($dataString);
            try {
                // write only if there are changes, to reduce I/O
                if (self::$cacheMutationFlag[$context]??null !== $checksum) {
                    self::$cacheMutationFlag[$context] = $checksum;

                    try {
                        // DEBUG ONLY
                        $this->debugSetNewCacheBag($context);
                    } catch (CacheException $e) {}

                    $instance->set($this->getGlobalCacheKey($context), $dataString, [], 0);
                }

            } catch (Exception $e) {
                return false;
            }
        }

        return true;
    }

    /**
     * @param string $context
     * @return void
     * @throws CacheException
     * @throws Exception\DuplicateIdentifierException
     * @throws Exception\InvalidBackendException
     * @throws Exception\InvalidCacheException
     */
    private function debugSetNewCacheBag(string $context): void
    {
        if (!Environment::getContext()->isDevelopment() || $context === self::DEBUG_CACHE_CONTEXT) {
            return;
        }

        $list = $this->debugGetCacheContextList();
        if (!in_array($context, $list)) {
            $list[] = $context;
            $this->set(self::DEBUG_CACHE_KEY_LIST, $list, self::DEBUG_CACHE_CONTEXT, 0);
        }
    }

    /**
     * @internal
     * @return array
     */
    public function debugGetCacheContextList(): array
    {
        $isNew = false;
        $list = [];
        try {
            $list = $this->get(self::DEBUG_CACHE_KEY_LIST, self::DEBUG_CACHE_CONTEXT, $isNew);
        } catch (Exception\DuplicateIdentifierException|Exception\InvalidCacheException|Exception\InvalidBackendException $e) {}
        if (!$isNew || !is_array($list)) {
            $list = [];
        }

        return $list;
    }

    /**
     * @internal
     * @param string $context
     * @return array
     */
    public function debugGetCacheContextContent(string $context): array
    {
        try {
            $this->initCacheBag($context);
        } catch (Exception\DuplicateIdentifierException|Exception\InvalidBackendException|Exception\InvalidCacheException $e) {
            return [];
        }

        return self::$cacheBag[$context]??[];
    }

    /**
     * @param string $context
     * @throws Exception\DuplicateIdentifierException
     * @throws Exception\InvalidBackendException
     * @throws Exception\InvalidCacheException
     */
    private function initCacheBag(string $context)
    {
        // read only if we don't have a copy in memory, to reduce I/O
        if (isset(self::$cacheBag[$context])) {
            return;
        }

        $data = [];
        $instance = $this->getCacheInstance();
        if ($instance !== null) {
            $rawData = $instance->get($this->getGlobalCacheKey($context));
            if ($rawData !== false && is_string($rawData)) {
                $data = unserialize($rawData);
            }
        }

        if (!is_array($data)) {
            $instance->remove($this->getGlobalCacheKey($context));
            $instance->flush();
            $data = [];
        }

        self::$cacheBag[$context] = $data;
    }

    /**
     * @param int|null $ttl
     * @return int
     */
    private function getExpireTime(int $ttl = null): int
    {
        if ($ttl === null) {
            $ttl = static::DEFAULT_TTL; // default
        }

        if ($ttl <= 0) {
            return 0;
        }

        return time() + $ttl;
    }

    /**
     * @param array $data
     * @return string
     */
    private function serializeData(array $data): string
    {
        $serializedData = [];
        foreach ($data as $key => $item) {
            $serializedData[$key][self::KEY_TTL] = $item[self::KEY_TTL];
            if (!($item[self::KEY_RAW]??true)) {
                $serializedData[$key][self::KEY_CONTENT] = serialize($item[self::KEY_CONTENT]);
            } else {
                $serializedData[$key][self::KEY_CONTENT] = $item[self::KEY_CONTENT];
            }
            $serializedData[$key][self::KEY_RAW] = true;
        }

        return serialize($serializedData);
    }

    /**
     * @param string $key
     * @return string
     */
    private function sanitizeKey(string $key): string
    {
        return $key;
    }

    /**
     * @param string $context
     * @return string
     */
    private function getGlobalCacheKey(string $context): string
    {
        return sha1($context);
    }
}
