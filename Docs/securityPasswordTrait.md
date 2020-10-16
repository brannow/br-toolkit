# Misc / Traits / Security / PasswordTrait

create secure password hashes based on the bcrypt algorytim (password_hash PASSWORD_DEFAULT)

##### Methods

* [hashPassword](#hashpassword)
* [verifyPassword](#verifypassword)

#### hashPassword
returns a selfsalted bcrypt password, the length for the same password can vary over time and can be up to 255 chars.

```php
protected function hashPassword(string $plainText, int $cost = 4): string
```

##### Arguments
 * `string $plainText`Plain raw password
 * `int $cost` password create cost, a good value between security and time is around 12 (default: 4)

##### Return
 * `string`

##### example
```php
class MyClass {
    use \BR\Toolkit\Misc\Traits\Security\PasswordTrait;
    public function password()
    {
        $plainPassword = 'pass6';
        $passwordHash = $this->hashPassword($plainPassword);
        // $passwordHash = $2y$04$hTUZDZw3AWO0J85mHetXlOL3T5Fu6GbcfW.ICHuZLA..b3bFE/076
    }
}
```

---

#### verifyPassword
checks if the plain password and hash are correct.

```php
protected function verifyPassword(string $plainText, string $hash): bool
```

##### Arguments
 * `string $plainText` plain password to check
 * `string $hash` password hash

##### Return
 * `bool`

##### example
```php
class MyClass {
    use \BR\Toolkit\Misc\Traits\Security\PasswordTrait;
    public function password()
    {
        $hash = '$2y$04$hTUZDZw3AWO0J85mHetXlOL3T5Fu6GbcfW.ICHuZLA..b3bFE/076';
        $password = 'pass6';
        $this->verifyPassword($password, $hash); // true
    }
}