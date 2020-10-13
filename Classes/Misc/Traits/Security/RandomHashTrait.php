<?php


namespace BR\Toolkit\Misc\Traits\Security;


trait RandomHashTrait
{
    /**
     * @return string
     */
    protected function getRandomSha1Hash(): string
    {
        return static::randomSha1Hash();
    }

    /**
     * random_bytes, with microtime seed
     * @return string
     */
    protected static function randomSha1Hash(): string
    {
        try {
            return sha1(random_bytes(20) . microtime());
        } catch (\Exception $e) {}

        return static::randomSha1HashFallback();
    }

    /**
     * random_bytes failed use uniqid, with microtime seed, instead
     * @return string
     */
    protected static function randomSha1HashFallback(): string
    {
        return sha1(uniqid((string)microtime(), true));
    }

    /**
     * @param string $hash
     * @return bool
     */
    protected function validateHash(string $hash): bool
    {
        return (bool)preg_match('/\b[0-9a-f]{40}\b/', $hash, $_);
    }
}