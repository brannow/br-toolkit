# Misc / Traits / Security / RandomHashTrait

create a random cryptographic secure sha1 hash

##### Methods

* [getRandomSha1Hash](#getrandomsha1hash)
* [randomSha1Hash](#randomsha1hash)
* [randomSha1HashFallback](#randomsha1hashfallback)
* [validateHash](#validatehash)

#### getRandomSha1Hash
alias of static method [randomSha1Hash()](#randomsha1hash)

```php
protected function getRandomSha1Hash(): string
```

##### Return
 * `string`

---

#### randomSha1Hash
create a random cryptographic secure sha1 hash

```php
protected static function randomSha1Hash(): string
```

##### Return
 * `string`

##### example
```php
class MyClass {
    use \BR\Toolkit\Misc\Traits\Security\RandomHashTrait;
    public function hash()
    {
        $hash = static::randomSha1Hash();
        // $hash = da39a3ee5e6b4b0d3255bfef95601890afd80709
    }
}
```

---

#### randomSha1HashFallback
same as [randomSha1Hash()](#randomsha1hash) but uses a not so secure method, (no compatibility problems)

```php
protected static function randomSha1HashFallback(): string
```

##### Return
 * `string`

##### example
```php
class MyClass {
    use \BR\Toolkit\Misc\Traits\Security\RandomHashTrait;
    public function hash()
    {
        $hash = static::randomSha1HashFallback();
        // $hash = da39a3ee5e6b4b0d3255bfef95601890afd80709
    }
}
```

#### validateHash
checks if the given `$hash` is a valid sha1 hash

```php
protected function validateHash(string $hash): bool
```

##### Arguments
 * `string $hash` hash to check

##### Return
 * `bool`

##### example
```php
class MyClass {
    use \BR\Toolkit\Misc\Traits\Security\RandomHashTrait;
    public function hash()
    {
        $this->validateHash('da39a3ee5e6b4b0d3255bfef95601890afd80709'); // true
        $this->validateHash('TEST'); // false
    }
}
```