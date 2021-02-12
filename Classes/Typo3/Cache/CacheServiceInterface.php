<?php


namespace BR\Toolkit\Typo3\Cache;


interface CacheServiceInterface
{
    public const CONTEXT_GLOBAL = 'global';

    /**
     * @param string $key
     * @param callable $block
     * @param string $context
     * @param int|null $ttl
     * @return mixed
     */
    public function cache(string $key, callable $block, string $context = CacheServiceInterface::CONTEXT_GLOBAL, int $ttl = null);

    /**
     * @param string $key
     * @param string $context
     * @return bool
     */
    public function destroy(string $key, string $context = CacheServiceInterface::CONTEXT_GLOBAL): bool;
}