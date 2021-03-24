# TYPO3 / Configuration / ConfigurationHandler

load the configuration for an extension.

##### Methods

* [getExtensionConfiguration](#getextensionconfiguration)
* [getExtensionTypoScript](#getextensiontyposcript)

#### getExtensionConfiguration
get the Extension Configuration (this does not include typoScript).

The Configuration is located in Configuration Module (until TYPO3 8 in ExtensionManager)   
see [TYPO3 Docs](https://docs.typo3.org/m/typo3/reference-coreapi/master/en-us/ExtensionArchitecture/ConfigurationOptions/Index.html) 
for more information
```php
public function getExtensionConfiguration(string $extName): ConfigurationBagInterface
```

##### Arguments
 * `string $extName` internal extension name (the same as the extension directory name)

##### Return
 * [`\BR\Toolkit\Typo3\DTO\Configuration\ConfigurationBagInterface`](/Docs/Structure/configuration.md)
 
##### example
```php
$handler = new \BR\Toolkit\Typo3\Configuration\ConfigurationHandler();
$value = $handler->getExtensionConfiguration('br_toolkit')->getValue('someValue');
```

#### getExtensionTypoScript
Loads the entire Page Typoscript once (after the first request the entire typoscript is stored in memory, for the entire runtime)

get the `plugin` & `module` part only.

```php
public function getExtensionTypoScript(string $extName): ConfigurationBagInterface
```

##### Arguments
* `string $extName` internal extension name (the same as the extension directory name)

##### Return
* [`\BR\Toolkit\Typo3\DTO\Configuration\ConfigurationBagInterface`](/Docs/Structure/configuration.md)

##### example
```php
$handler = new \BR\Toolkit\Typo3\Configuration\ConfigurationHandler();
$value = $handler->getExtensionTypoScript('br_toolkit')->getValueFromArrayPath('plugin.settings.value1');
```