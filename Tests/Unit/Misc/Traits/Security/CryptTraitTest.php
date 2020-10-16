<?php

namespace BR\Toolkit\Tests\Misc\Traits\Security;


use BR\Toolkit\Misc\Traits\Security\CryptTrait;
use PHPUnit\Framework\TestCase;

class CryptBridge
{
    use CryptTrait;

    /**
     * @param string $content
     * @param string $secret
     * @param string $salt
     * @return string
     */
    public static function _encrypt(string $content, string $secret, string $salt = ''): string
    {
        return static::encrypt($content, $secret, $salt);
    }

    /**
     * @param string $data
     * @param string $secret
     * @param string $salt
     * @return string|null
     */
    public static function _decrypt(string $data, string $secret, string $salt = ''): ?string
    {
        return static::decrypt($data, $secret, $salt);
    }
}

class CryptTraitTest extends TestCase
{
    /**
     * @test
     */
    public function testMD5SynthTest()
    {
        $secret = 'superSecret';
        for ($i = 0; $i < 20; $i++) {
            $random = md5(random_bytes(10));
            $d = CryptBridge::_encrypt($random, $secret);
            $rev = CryptBridge::_decrypt($d, $secret);
            $this->assertEquals($random, $rev);
        }
    }

    /**
     * @test
     */
    public function testRandomLengthSynthTest()
    {
        $secret = 'superSecret';
        for ($i = 0; $i < 20; $i++) {
            $random = md5(random_bytes(10));
            $random = substr($random, 0, $i+1);
            $d = CryptBridge::_encrypt($random, $secret);
            $rev = CryptBridge::_decrypt($d, $secret);
            $this->assertEquals($random, $rev);
        }
    }

    public function testEncrypt()
    {
        $secret = 'superSecret';
        for ($i = 0; $i < 100; $i++) {
            $str = CryptBridge::_encrypt(str_pad('1', $i, '1'), $secret);
            $this->assertEquals(1, substr_count($str, ':'));
            $this->assertEquals(0, substr_count($str, '='));
        }
    }

    public function testDecrypt()
    {
        $secret = 'superSecret';
        for ($i = 0; $i < 100; $i++) {
            $base = str_pad('1', $i, '1');
            $str = CryptBridge::_encrypt($base, $secret);
            $recover = CryptBridge::_decrypt($str, $secret);
            $this->assertEquals($base, $recover);
        }
    }

    public function testDecryptWithSalt()
    {
        $salt = 'salt';
        $secret = 'superSecret';
        for ($i = 0; $i < 100; $i++) {
            $base = str_pad('1', $i, '1');
            $str = CryptBridge::_encrypt($base, $secret, $salt.$i);
            $recover = CryptBridge::_decrypt($str, $secret, $salt.$i);
            $this->assertSame($base, $recover);
        }
    }

    public function testDecryptWithSaltFailed()
    {
        $text = 'test';
        $str = CryptBridge::_encrypt($text, 'secret', 'saltA');
        $recover = CryptBridge::_decrypt($str, 'secret', 'saltB');
        $this->assertNull($recover);
    }

    public function testDecryptMismatchSecret()
    {
        $secret = 'superSecret';
        $otherSecret = 'notTheOriginalSecret';
        for ($i = 0; $i < 10; $i++) {
            $base = str_pad('1', $i, '1');
            $str = CryptBridge::_encrypt($base, $secret);
            $recover = CryptBridge::_decrypt($str, $otherSecret);
            $this->assertNotEquals($base, $recover);
        }
    }

    public function testDecryptFailedRandomStuff()
    {
        $secret = 'superSecret';
        $otherSecret = 'notTheEncryptedDataWeExpected';
        for ($i = 0; $i < 10; $i++) {
            $recover = CryptBridge::_decrypt($otherSecret, $secret);
            $this->assertNull($recover);
        }
    }
}