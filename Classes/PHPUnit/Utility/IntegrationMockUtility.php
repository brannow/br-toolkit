<?php

namespace BR\Toolkit\PHPUnit\Utility;

use PHPUnit\Framework\MockObject\MockBuilder;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use ReflectionException;

abstract class IntegrationMockUtility
{
    private static array $mockCache = [];
    private static array $dependencyCache = [];
    private static array $classAliases = [];

    /**
     * @template T of object
     * @param TestCase $testCase
     * @param string $className
     * @param array $mockList
     * @param array $dependencyList
     * @param array $classAliases
     * @param MockObject[] $injectMockObjects
     * @return T the created instance
     * @throws ReflectionException
     */
    public static function createObjectWithDependencies(TestCase $testCase, string $className, array &$mockList, array &$dependencyList, array $classAliases = [], array $injectMockObjects = []): object
    {
        $mockList = array_unique(array_merge($mockList, array_keys($injectMockObjects)));
        self::$mockCache = $injectMockObjects;
        self::$dependencyCache = [];
        self::$classAliases = $classAliases;
        $obj = static::initClassNameWithDependencies($testCase, $className, $mockList);

        $mockList = self::$mockCache;
        $dependencyList = self::$dependencyCache;

        return $obj;
    }

    /**
     * @template T of object
     * @param TestCase $testCase
     * @param string|class-string<T> $orgClassName
     * @param array $mockList
     * @return T
     * @throws ReflectionException
     */
    private static function initClassNameWithDependencies(TestCase $testCase, string $orgClassName, array $mockList): object
    {
        // remap class alias
        if (!empty(self::$classAliases[$orgClassName])) {
            $className = self::$classAliases[$orgClassName];
        } else {
            $className = $orgClassName;
        }

        // check if is it mockable
        if (in_array($className, $mockList, true) || in_array($orgClassName, $mockList, true)) {
            return self::createMockObject($testCase, $className);
        }

        $arguments = self::getClassConstructorArguments($testCase, $className, $mockList);
        return self::$dependencyCache[$className] = new $className(...$arguments);
    }

    /**
     * @param TestCase $testCase
     * @param string $className
     * @return MockObject
     */
    private static function createMockObject(TestCase $testCase, string $className): MockObject
    {
        return self::$mockCache[$className] ?? (
            self::$mockCache[$className] =
                (new MockBuilder($testCase ,$className))
                    ->disableOriginalConstructor()
                    ->getMock()
            );
    }

    /**
     * @param TestCase $testCase
     * @param string $className
     * @param array $mockList
     * @return array
     * @throws ReflectionException
     */
    private static function getClassConstructorArguments(TestCase $testCase, string $className, array $mockList): array
    {
        $class = new \ReflectionClass($className);
        $methodRef = $class->getConstructor();
        if ($methodRef === null) {
            return [];
        }

        $arg = [];
        foreach($methodRef->getParameters() AS $argumentRef) {
            $subClassName = $argumentRef->getType()->getName();
            if (empty($subClassName) && !class_exists($subClassName)) {
                continue;
            }
            $arg[] = self::initClassNameWithDependencies($testCase, $subClassName, $mockList);
        }

        return $arg;
    }
}
