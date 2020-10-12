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
     * @return string
     */
    protected static function randomSha1Hash(): string
    {
        try {
            return sha1(random_bytes(20) . microtime());
        } catch (\Exception $e) {}

        return sha1(uniqid((string)mktime(), true));
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