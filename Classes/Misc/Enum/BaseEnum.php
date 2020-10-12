<?php


namespace BR\Toolkit\Misc\Enum;


abstract class BaseEnum
{
    /**
     * @var array
     * @internal
     */
    protected static $valueCache = [];

    /**
     * @var array
     * @internal
     */
    protected static $valueCacheCheckSum = [];

    /**
     * @return array
     */
    public static function getValues(): array
    {
        if (empty(static::$valueCache[static::class])) {
            static::$valueCache[static::class] = array_values(
                (new \ReflectionClass(static::class))->getConstants()
            );
        }

        return static::$valueCache[static::class];
    }

    /**
     * @return array
     */
    protected static function getHashValues(): array
    {
        if (empty(static::$valueCacheCheckSum[static::class])) {
            foreach (static::getValues() as $value) {
                static::$valueCacheCheckSum[static::class][static::convertValueToChecksum($value)] = $value;
            }
        }

        return static::$valueCacheCheckSum[static::class];
    }

    /**
     * @param $value
     * @return bool
     */
    public static function validate($value): bool
    {
        return array_key_exists(static::convertValueToChecksum($value), static::getHashValues());
    }

    /**
     * @param $value
     * @param $fallback
     * @return mixed
     */
    public static function sanitize($value, $fallback)
    {
        if (static::validate($value)) {
            return static::getHashValues()[static::convertValueToChecksum($value)]??$fallback;
        }

        return $fallback;
    }

    /**
     * @param $value
     * @return int
     */
    protected static function convertValueToChecksum($value): int
    {
        if (is_numeric($value) || is_string($value)) {
            return crc32((string)$value);
        }

        return crc32(serialize($value));
    }
}