# Misc / Traits / Security / CryptTrait

Cryptographic strong encryption / decryption. (AES 128 GCM)     
The Encrypted data will be Base64~ish encoded.

The Encrypted data string is based on 2 base64 chunks concated with a colon `:`.
* the first part is the actual payload
* the second part is the given `IV` (Initialization Vector)

both parts are base64 encoded (url notation, known as `base64url`). 
 `/` -> `_`,
 `+` -> `-`
are replaced.    
Also the tailing `=` is removed with a integer that says how many `=` are there.
```
0 -> ''
1 -> '='
2 -> '=='
... etc.
```

the Text `test` with the secret `secret`, will create   
`ehEa9Q2:AqwKKBzuCAlzXFH-_b8JAw2`

the 2 base 64 parts are:     
payload: `ehEa9Q2`     
IV: `AqwKKBzuCAlzXFH-_b8JAw2`
 
Base64URL changes reverted (plain base64):     
payload: `ehEa9Q==`     
IV: `AqwKKBzuCAlzXFH+/b8JAw==` 

these are the original data which can via AES_128_GCM and the given secret `secret` decoded into `test`   

encryption / decryption takes some time:   
 1000 chars plaintext with a 1000 char secret needs 
 * encrypt: `~0.18ms`
 * decrypt: `~0.17ms`
 
 (this may vary on between servers)

##### Methods

* [encrypt](#encrypt)
* [decrypt](#decrypt)

#### encrypt
encrypt

```php
protected static function encrypt(string $content, string $secret, string $salt = ''): string
```

##### Arguments
 * `string $content` plain text that will be encrypted
 * `string $secret` PRIVATE SECRET (never publish or loose it)
 * `string $salt` for the extra saltiness, same rules as `$secret`

##### Return
 * `string`

##### example
```php
class MyClass {
    use \BR\Toolkit\Misc\Traits\Security\CryptTrait;
    public function encryptMessage(int $userId, string $message): string
    {
        return static::encrypt($message, 'secret', md5($userId));
    }
}
```

---

#### decrypt
check if the `$value` is in Enum (not type strict!)

```php
protected static function decrypt(string $data, string $secret, string $salt = ''): ?string
```

##### Arguments
 * `string $content` plain text that will be encrypted
 * `string $secret` PRIVATE SECRET (never publish or loose it)
 * `string $salt` for the extra saltiness, same rules as `$secret`

##### Return
 * `string|null`

##### example
```php
class MyClass {
    use \BR\Toolkit\Misc\Traits\Security\CryptTrait;
    public function decryptMessage(int $senderUserId, string $encryptedMessage): string
    {
        return static::decrypt($encryptedMessage, 'secret', md5($senderUserId)) ?? '';
    }
}
```