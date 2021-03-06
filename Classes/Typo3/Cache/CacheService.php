<?php


namespace BR\Toolkit\Typo3\Cache;


use BR\Toolkit\Exceptions\CacheException;
use BR\Toolkit\Typo3\DTO\Configuration\ConfigurationBag;
use BR\Toolkit\Typo3\DTO\Configuration\ConfigurationBagInterface;
use BR\Toolkit\Typo3\VersionWrapper\InstanceUtility;
use TYPO3\CMS\Core\Cache\CacheManager as Typo3CacheManager;
use TYPO3\CMS\Core\Cache\Exception;
use TYPO3\CMS\Core\Cache\Exception\NoSuchCacheException;
use TYPO3\CMS\Core\Cache\Backend\BackendInterface;

class CacheService implements CacheServiceInterface
{
    private const NOT_FOUND_BLOCK = 'NOT_FOUND_TRIGGER_BLOCK_0x011011';

    // in seconds
    protected const DEFAULT_TTL = 3600;

    /**
     * @var string
     */
    private static $cacheKey = '';

    /**
     * @var BackendInterface
     */
    private $cacheInstance;

    /**
     * @var ConfigurationBagInterface[]
     */
    private static $cacheBag = [];

    /**
     * @var array
     */
    private static $cacheMutationFlag = [];

    /**
     * CacheService constructor.
     * @param Typo3CacheManager|null $typo3CacheManager
     */
    public function __construct(Typo3CacheManager $typo3CacheManager = null)
    {
        try {
            $typo3CacheManager = $typo3CacheManager??InstanceUtility::get(Typo3CacheManager::class);
            $this->cacheInstance = $typo3CacheManager->getCache(CacheManager::CACHE_DOMAIN)->getBackend();
        } catch (NoSuchCacheException $e) {}
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
     */
    public function destroy(string $key, string $context = CacheServiceInterface::CONTEXT_GLOBAL): bool
    {
        $this->initCacheBag($context);
        $data = self::$cacheBag[$context]->getData();

        if (!isset($data[$key])) {
            return false;
        }
        unset($data[$key]);
        self::$cacheBag[$context] = new ConfigurationBag($data);
        return $this->storeCacheBag($context);
    }

    /**
     * @param string $key
     * @param string $context
     * @param bool $exists
     * @return array|false|mixed|string
     */
    private function get(string $key, string $context ,bool &$exists)
    {
        $exists = true;
        $this->initCacheBag($context);
        $content = self::$cacheBag[$context]->getValueFromArrayPath($key . '.content', self::NOT_FOUND_BLOCK);
        $ttl = (int)self::$cacheBag[$context]->getValueFromArrayPath($key . '.ttl', -1);

        if ($ttl > 0 && time() > $ttl) {
            $this->destroy($key, $context);
            $content = self::NOT_FOUND_BLOCK;
        }

        if ($content === self::NOT_FOUND_BLOCK) {
            $exists = false;
            return '';
        }

        return $content;
    }

    /**
     * @param string $key
     * @param $content
     * @param string $context
     * @param int|null $ttl
     * @throws CacheException
     */
    private function set(string $key, $content, string $context, int $ttl = null)
    {
        if ($content === self::NOT_FOUND_BLOCK) {
            throw new CacheException('content cannot be stored, \''. self::NOT_FOUND_BLOCK .'\' is reserved as internal trigger message');
        }

        $this->initCacheBag($context);
        $data = self::$cacheBag[$context]->getData();
        $data[$key]['content'] = $content;
        $data[$key]['ttl'] = $this->getExpireTime($ttl);
        self::$cacheBag[$context] = new ConfigurationBag($data);
        $this->storeCacheBag($context);
    }

    /**
     * @param string $context
     * @return bool
     */
    private function storeCacheBag(string $context): bool
    {
        $this->initCacheBag($context);
        $data = self::$cacheBag[$context]->getData();
        if ($this->cacheInstance !== null) {
            $dataString = serialize($data);
            $checksum = crc32($dataString);
            try {
                // write only if there are changes, to reduce I/O
                if (self::$cacheMutationFlag[$context]??null !== $checksum) {
                    self::$cacheMutationFlag[$context] = $checksum;
                    $this->cacheInstance->set($this->getGlobalCacheKey($context), $dataString, [], 0);
                }

            } catch (Exception $e) {
                return false;
            }
        }

        return true;
    }

    /**
     * @param string $context
     */
    private function initCacheBag(string $context)
    {
        // read only if we dont have a copy in memory, to reduce I/O
        if (isset(self::$cacheBag[$context])) {
            return;
        }

        $data = [];
        if ($this->cacheInstance !== null) {
            $rawData = $this->cacheInstance->get($this->getGlobalCacheKey($context));
            if ($rawData !== false && is_string($rawData)) {
                $data = unserialize($rawData);
            }
        }

        self::$cacheBag[$context] = new ConfigurationBag($data);
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

        $currentTime = time();
        return $currentTime + $ttl;
    }

    /**
     * @param string $context
     * @return string
     */
    private function getGlobalCacheKey(string $context): string
    {
        if (self::$cacheKey === '') {
            self::$cacheKey = sha1(__CLASS__.__FILE__.$context);
        }
        return self::$cacheKey;
    }
}
