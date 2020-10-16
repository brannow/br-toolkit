# PHPUnit / Utility / IntegrationMockUtility

Emulates Dependency Injection via constuctors, with the ability to replace explicit classes as Mocked objects.

All Objects and Mocks which are used will be in the test Available. 

##### Methods

* [getRequest](#getrequest)
* [setRequest](#setrequest)

#### createObjectWithDependencies
Creates an object of the requested Class with all dependencies included (constructor inject only)
All Mocks / Dependencies are returned.

Mocklist / Alias list elements who are not used will be ignored
```php
public static function createObjectWithDependencies(
    TestCase $testCase, 
    string $className, 
    array &$mockList, 
    array &$dependencyList, 
    array $classAliases = []
): object|MockObject
```

##### Arguments
 * `TestCase $testCase` Current TestCase Instance object, mostly `$this` in TestCases
 * `string $className` Actual className of the object that will generated
 * `array &$mockList` by-reference: simple list that contains all classes/interfaces that must be replaced with mocked objects
 * `array &$dependencyList` by-reference: will be filled with all `[classname => object]` that are used (dependencies)
 * `array $classAliases` optional assoc array for Interfaces to replace with Classes, for example
 

##### Return
 * `object|MockObject` an object of `$className`
 

##### example
Sample usage:
```php
class AjaxMiddlewareTest extends TestCase
{
    protected function setUp()
    {
        $mockList = [
            ConfigurationHandler::class,
            Curl::class
        ];
        $dependencyList = [];

        $ajaxMiddleWare = MockHelper::createObjectWithDependencies(
            $this,
            AjaxController::class,
            $mockList,
            $dependencyList
        );
       
        print_r($mockList);
        /* output:
        * [
        *   ConfigurationHandler::class => {MOCK_OBJECT_ConfigurationHandler},
        *   Curl::class => {MOCK_OBJECT_Curl},
        * ]
        */
        
        print_r($dependencyList);
        /* output:
        * [
        *   AjaxController::class => {OBJECT_AjaxController},
        *   OtherInjectedClasses::class => {OBJECT_OtherInjectedClasses},
        *   ...
        * ]
        */
    }
}
```

With Alias usage:
```php
class AjaxMiddlewareTest extends TestCase
{
    protected function setUp()
    {
        $aliases = [
            ConfigurationHandler::class => OtherConfigurationHandler::class,
            FactoryInterface::class => MyFactory::class
        ];
        $mockList = [
            ConfigurationHandler::class,
            Curl::class
        ];
        $dependencyList = [];

        $ajaxMiddleWare = MockHelper::createObjectWithDependencies(
            $this,
            AjaxController::class,
            $mockList,
            $dependencyList,
            $aliases
        );
        
        $factoryMock = $mockList[Curl::class];
        $factoryMock->expects($this->any())
            ->method('execute')
            ->willReturn('HELLO WORLD'); 
       
        print_r($mockList);
        /* output:
        * [
        *   OtherConfigurationHandler::class => {MOCK_OBJECT_OtherConfigurationHandler},
        *   Curl::class => {MOCK_OBJECT_Curl},
        * ]
        */
        
        print_r($dependencyList);
        /* output:
        * [
        *   AjaxController::class => {OBJECT_AjaxController},
        *   MyFactory::class => {OBJECT_MyFactory},
        *   ...
        * ]
        */
    }
}
```
 