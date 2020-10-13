<?php

namespace BR\Toolkit\Tests\Misc\Traits\Security;


use BR\Toolkit\Misc\Traits\Security\PasswordTrait;
use PHPUnit\Framework\TestCase;

class PasswordTraitBridge
{
    use PasswordTrait;

    /**
     * @param string $plainText
     * @param int $cost
     * @return string
     */
    public function hashPasswordBridge(string $plainText, int $cost = 4): string
    {
        return $this->hashPassword($plainText, $cost);
    }

    /**
     * @param string $plainPassword
     * @param string $hash
     * @return bool
     */
    public function verifyPasswordBridge(string $plainPassword, string $hash): bool
    {
        return $this->verifyPassword($plainPassword, $hash);
    }
}

class PasswordTraitTest extends TestCase
{
    public function passwordHashProvider(): array
    {
        return [
            ['pass1', -1],
            ['pass2', 0],
            ['pass3', 1],
            ['pass4', 2],
            ['pass5', 3],
            ['pass6', 4],
            ['pass7', 5]
        ];
    }

    /**
     * @dataProvider passwordHashProvider
     * @param string $rawPW
     * @param int $cost
     */
    public function testPasswordHash(string $rawPW, int $cost)
    {
        $trait = new PasswordTraitBridge();
        $hash = $trait->hashPasswordBridge($rawPW, $cost);
        $this->assertNotEmpty($hash);
    }

    /**
     * @dataProvider passwordHashProvider
     * @param string $rawPW
     * @param int $cost
     */
    public function testPasswordHashVerify(string $rawPW, int $cost)
    {
        $trait = new PasswordTraitBridge();
        $hash = $trait->hashPasswordBridge($rawPW, $cost);
        $this->assertTrue($trait->verifyPasswordBridge($rawPW, $hash));
    }

    /**
     * @dataProvider passwordHashProvider
     * @param string $rawPW
     * @param int $cost
     */
    public function testPasswordHashVerifyFail(string $rawPW, int $cost)
    {
        $trait = new PasswordTraitBridge();
        $hash = $trait->hashPasswordBridge($rawPW, $cost);
        $this->assertFalse($trait->verifyPasswordBridge($rawPW . time(), $hash));
    }
}