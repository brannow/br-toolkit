# TYPO3 / Configuration

used for easy Typo3 Configuration Loading (see: `BR\Toolkit\Typo3\Configuration\ConfigurationHandler`) 

## Classes

* `BR\Toolkit\Typo3\DTO\Configuration\ConfigurationBag`

## Interfaces

* `BR\Toolkit\Typo3\DTO\Configuration\ConfigurationBagInterface`

### ConfigurationBagInterface

* [getValue](#getvalue)
* [getValueFromArrayPath](#getvaluefromarraypath)
* [getExplodedIntValue](#getexplodedintvalue)
* [getExplodedIntValueFromArrayPath](#getexplodedintvaluefromarraypath)

#### getValue
get the config value of the given key.
```php
public function getValue(
    string $key, 
    mixed $default = ''
): mixed
```
##### Arguments
* `string $key` name of the value
* `mixed $default` default return value if `$key` is not found
 
##### Return
 * `mixed`
 
##### Example
```php
$configBag = new \BR\Toolkit\Typo3\DTO\Configuration\ConfigurationBag(['name' => 'test']);
$name = $configBag->getValue('name', 'unknown');
// $name = test
```
```php
$configBag = new \BR\Toolkit\Typo3\DTO\Configuration\ConfigurationBag(['otherKey' => 'test']);
$name = $configBag->getValue('name', 'unknown');
// $name = unknown
```

---

#### getValueFromArrayPath
get the config value of the given array keyPath.     
```php
public function getValueFromArrayPath(
    string $path, 
    mixed $default = '', 
    string $delimiter = '.'
): mixed
```

##### Arguments
* `string $path` name of the value as path
* `mixed $default` default return value if `$path` is not found (default `''`, \[empty string\])
* `string $delimiter` delimiter of the `$path` segments (default `.`)
 
##### Return
 * `mixed`

##### Example
```php
$configBag = new \BR\Toolkit\Typo3\DTO\Configuration\ConfigurationBag(['settings' => ['default' => 5]]);
$default = $configBag->getValueFromArrayPath('settings.default', 0);
// $default = 5

$default = $configBag->getValueFromArrayPath('settings|default', 0, '|');
// $default = 5
```
```php
$configBag = new \BR\Toolkit\Typo3\DTO\Configuration\ConfigurationBag(['settings' => ['special' => 5]]);
$default = $configBag->getValueFromArrayPath('settings.default', 0);
// $default = 0
```

---

#### getExplodedIntValue
get the config value of the given key as `int[]`. The value is expected as separated numeric list
```php
public function getExplodedIntValue(
    string $key, 
    string $delimiter = ','
): array
```

##### Arguments
* `string $key` name of the value
* `mixed $delimiter` delimiter of the `value` for the `$key` (default `.`)
 
##### Return
* `int[]`

##### Example
```php
$configBag = new \BR\Toolkit\Typo3\DTO\Configuration\ConfigurationBag(['settings' => '1|2|3']);
$settings = $configBag->getExplodedIntValue('settings', '|');
// $settings = [1,2,3]
```
```php
$configBag = new \BR\Toolkit\Typo3\DTO\Configuration\ConfigurationBag(['settings' => '1,2,3']);
$default = $configBag->getValueFromArrayPath('settings', '|');
// $default = []
```
```php
$configBag = new \BR\Toolkit\Typo3\DTO\Configuration\ConfigurationBag(['settings' => '1,a,3']);
$default = $configBag->getValueFromArrayPath('settings', ',');
// $default = [1,3]
```

---

#### getExplodedIntValueFromArrayPath
get the config value of the given array keyPath as `int[]`. The value is expected as separated numeric list
```php
public function getExplodedIntValueFromArrayPath(
    string $path, 
    string $pathDelimiter = '.', 
    string $listDelimiter = ','
): array
```

##### Arguments
* `string $path` name of the value as path
* `mixed $pathDelimiter` delimiter of the `$path` segments (default `.`)
* `string $listDelimiter` delimiter of the `value` for the `$key` (default `.`)

##### Return
* `int[]`

##### Example
```php
$configBag = new \BR\Toolkit\Typo3\DTO\Configuration\ConfigurationBag(['settings' => ['test' => '1,a,3']]);
$default = $configBag->getExplodedIntValueFromArrayPath('settings.test', '.', ',');
// $default = [1,3]
```


