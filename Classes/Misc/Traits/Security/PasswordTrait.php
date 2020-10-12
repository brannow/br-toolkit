<?php


namespace BR\Toolkit\Misc\Traits\Security;

trait PasswordTrait
{
    /**
     * @param string $plainText
     * @param int $cost
     * @return string
     */
    protected function hashPassword(string $plainText, int $cost = 4): string
    {
        if ($cost < 4) {$cost = 4;}
        return password_hash($plainText, PASSWORD_DEFAULT, ['cost' => $cost]);
    }

    /**
     * @param string $plainText
     * @param string $hash
     * @return bool
     */
    protected function verifyPassword(string $plainText, string $hash): bool
    {
        return password_verify($plainText, $hash);
    }
}