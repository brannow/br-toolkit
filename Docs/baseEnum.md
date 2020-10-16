# Misc / Enum / BaseEnum

Enum Foundation used as parent class

##### Methods

* [getValues](#getvalues)
* [validate](#validate)
* [sanitize](#sanitize)


#### getValues
get all const Values from the Enum

```php
public static function getValues(): array
```

##### Return
 * `array`

##### example
```php
class MyEnum extends \BR\Toolkit\Misc\Enum\BaseEnum 
{
    public const ENUM_A = 'a';
    public const ENUM_B = 'b';
    public const ENUM_C = 'c';
}

print_r(MyEnum::getValues());

Output:
    Array
      (
          [0] => a
          [1] => b
          [2] => c
      )
```

---

#### validate
check if the `$value` is in Enum (not type strict!)

```php
public static function validate($value): bool
```

##### Arguments
 * `mixed $value` value zu check

##### Return
 * `bool`

##### example
```php
class MyEnum extends \BR\Toolkit\Misc\Enum\BaseEnum 
{
    public const ENUM_A = '0';
    public const ENUM_B = 1.0;
    public const ENUM_C = 'a';
}

MyEnum::validate(0); // true
MyEnum::validate('1'); // true
MyEnum::validate('a'); // true
MyEnum::validate('1.0'); // false
```

---

#### sanitize
Validate & sanitize `$value`.    
This will return the original (strict type) value of the enum,     
if not found, `$fallback` will returned 

```php
public static function sanitize($value, $fallback)
```

##### Arguments
 * `mixed $value` value zu check
 * `mixed $fallback` fallback if `$value` is not matchable (default: `NULL`)

##### Return
 * `mixed`

##### example
```php
class MyEnum extends \BR\Toolkit\Misc\Enum\BaseEnum 
{
    public const ENUM_A = '0';
    public const ENUM_B = 1.0;
    public const ENUM_C = 'a';
}

MyEnum::sanitize(0, 1);         // return: '0'
MyEnum::sanitize('1', 0);       // return: 1.0
MyEnum::sanitize('a', '');      // return: 'a'
MyEnum::sanitize('1.0', 0.0);   // return: 0.0
```