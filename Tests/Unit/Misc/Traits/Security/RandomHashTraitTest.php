<?php

namespace BR\Toolkit\Tests\Misc\Traits\Security;

use BR\Toolkit\Misc\Traits\Security\RandomHashTrait;
use PHPUnit\Framework\TestCase;

class RandomHashTraitBridge
{
    use RandomHashTrait;

    /**
     * @return string
     */
    public function getRandomSha1HashBridge(): string
    {
        return $this->getRandomSha1Hash();
    }

    /**
     * @return string
     */
    public static function randomSha1HashBridge(): string
    {
        return static::randomSha1Hash();
    }
    /**
     * @return string
     */
    public static function randomSha1HashFallbackBridge(): string
    {
        return static::randomSha1HashFallback();
    }

    /**
     * @param string $hash
     * @return bool
     */
    public function validateHashBridge(string $hash): bool
    {
        return $this->validateHash($hash);
    }
}

class RandomHashTraitTest extends TestCase
{
    /**
     *
     */
    public function testValidateSHA1()
    {
        $trait = new RandomHashTraitBridge();
        $this->assertTrue($trait->validateHashBridge('da39a3ee5e6b4b0d3255bfef95601890afd80709'));
        $this->assertTrue($trait->validateHashBridge(sha1('test')));
        $this->assertFalse($trait->validateHashBridge(md5('test')));
        $this->assertFalse($trait->validateHashBridge(hash('sha256', 'test')));
        $this->assertFalse($trait->validateHashBridge(hash('sha512', 'test')));
    }

    /**
     *
     */
    public function testRandomHashGeneration()
    {
        $trait = new RandomHashTraitBridge();
        for ($i = 0; $i < 10; $i++) {
            $hash = $trait->getRandomSha1HashBridge();
            $this->assertTrue($trait->validateHashBridge($hash));

            $hash = RandomHashTraitBridge::randomSha1HashBridge();
            $this->assertTrue($trait->validateHashBridge($hash));

            $hash = RandomHashTraitBridge::randomSha1HashFallbackBridge();
            $this->assertTrue($trait->validateHashBridge($hash));
        }
    }
}