<?php

namespace BR\Toolkit\Tests\Helper;

use PHPUnit\Framework\MockObject\MockBuilder;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class MockHelper
{
    private static $mockCache = [];
    private static $dependencyCache = [];
    private static $classAliases = [];

    /**
     * @param TestCase $testCase
     * @param string $className
     * @param array $mockList
     * @param array $dependencyList
     * @param array $classAliases
     * @return object|MockObject
     * @throws \ReflectionException
     */
    public static function createObjectWithDependencies(TestCase $testCase, string $className, array &$mockList, array &$dependencyList, array $classAliases = [])
    {
        self::$mockCache = [];
        self::$dependencyCache = [];
        self::$classAliases = $classAliases;
        $obj = static::initClassNameWithDependencies($testCase, $className, $mockList);

        $mockList = self::$mockCache;
        $dependencyList = self::$dependencyCache;

        return $obj;
    }

    /**
     * @param TestCase $testCase
     * @param string $orgClassName
     * @param array $mockList
     * @return object|MockObject
     * @throws \ReflectionException
     */
    private static function initClassNameWithDependencies(TestCase $testCase, string $orgClassName, array $mockList)
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
        $obj = new $className(...$arguments);
        self::$dependencyCache[$className] = $obj;
        return $obj;
    }

    /**
     * @param TestCase $testCase
     * @param string $className
     * @return MockObject
     */
    private static function createMockObject(TestCase $testCase, string $className): MockObject
    {
        if (!empty(self::$mockCache[$className])) {
            return self::$mockCache[$className];
        }

        $mockBuilder = new MockBuilder($testCase ,$className);
        $mo = $mockBuilder->disableOriginalConstructor()->getMock();
        self::$mockCache[$className] = $mo;
        return $mo;
    }

    /**
     * @param TestCase $testCase
     * @param string $className
     * @param array $mockList
     * @return array
     * @throws \ReflectionException
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
            $class = $argumentRef->getClass();
            $subClassName = $class->getName();
            if (!empty($subClassName)) {
                $arg[] = self::initClassNameWithDependencies($testCase, $subClassName, $mockList);
            }
        }

        return $arg;
    }
}