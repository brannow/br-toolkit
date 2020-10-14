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
get the config value of the given key, if key not found, `$default` is returned
```php
public function getValue(
    string $key, 
    mixed $default = ''
): mixed
```

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


#### getValueFromArrayPath
get the config value of the given array keyPath, if keyPath could not resolved or found, `$default` is returned.     
`$delimiter` is the keyPath Delimiter.
```php
public function getValueFromArrayPath(
    string $path, 
    mixed $default = '', 
    string $delimiter = '.'
): mixed
```

##### Example
```php
$configBag = new \BR\Toolkit\Typo3\DTO\Configuration\ConfigurationBag(['settings' => ['default' => 5]]);
$default = $configBag->getValueFromArrayPath('settings.default', 0);
// $default = 5
```
```php
$configBag = new \BR\Toolkit\Typo3\DTO\Configuration\ConfigurationBag(['settings' => ['special' => 5]]);
$default = $configBag->getValueFromArrayPath('settings.default', 0);
// $default = 0
```

#### getExplodedIntValue
get the config value of the given key as `int[]`. the value will be transformed into a numeric array.        
If no valid value for the given key found, an empty array is returned.    
`$delimiter` is the delimiter of the value list
```php
public function getExplodedIntValue(
    string $key, 
    string $delimiter = ','
): array
```

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


#### getExplodedIntValueFromArrayPath
get the config value of the given array keyPath as `int[]`, if keyPath could not resolved or found, an empty array is returned.      
`$pathDelimiter` is the keyPath Delimiter.     
`$listDelimiter` is the delimiter of the value list.
```php
public function getExplodedIntValueFromArrayPath(
    string $path, 
    string $pathDelimiter = '.', 
    string $listDelimiter = ','
): array
```

##### Example
```php
// config: ['settings' => ['default' => 5]]
$configBag = new \BR\Toolkit\Typo3\DTO\Configuration\ConfigurationBag(['settings' => ['test' => '1|a|3']]);
$default = $configBag->getExplodedIntValueFromArrayPath('settings|default', '|', '|');
// $default = [1,3]
```


