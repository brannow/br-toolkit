<?php

namespace BR\Toolkit\PHPUnit\Utility;

use BR\Toolkit\PHPUnit\Utility\DTO\MockDemand;
use BR\Toolkit\PHPUnit\Utility\DTO\MockResult;
use BR\Toolkit\PHPUnit\Utility\DTO\MockResultItem;
use PHPUnit\Framework\MockObject\MockBuilder;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

abstract class MockObjectUtility
{
    /**
     * @param TestCase $testCase
     * @param MockDemand $mockDemand
     * @return object|MockObject
     * @throws \ReflectionException
     */
    public static function getMock(TestCase $testCase, MockDemand $mockDemand): MockResult
    {
        $result = new MockResult();
        $result->setRootResultItem($rootResultItem = new MockResultItem($mockDemand->getClassName()));
        $rootResultItem->setObject(self::initWithMockDemand($testCase, $mockDemand, $rootResultItem));
        return $result;
    }

    private static function initWithMockDemand(TestCase $testCase, MockDemand $mockDemand, MockResultItem $resultItem): Object
    {
        if(($aliasDep = $mockDemand->getMockAliasDependency($resultItem)) !== null) {
            $resultItem->setAliasClassName($aliasDep->getAliasClassName());
        }

        if ($mockDemand->isMockable($resultItem)) {
            return self::getMockObject($testCase, $mockDemand, $resultItem);
        }

        return self::getRealClassObject($testCase, $mockDemand, $resultItem);
    }

    /**
     * @param TestCase $testCase
     * @param MockResultItem $resultItem
     * @return MockObject
     */
    protected static function getMockObject(TestCase $testCase, MockDemand $demand, MockResultItem $resultItem): MockObject
    {
        if(($beforeCallable = $demand->getBeforeObjectCreateCallable()) !== null) {
            $beforeCallable($resultItem, $demand, 'mock');
        }
        $object = (new MockBuilder($testCase, $resultItem->getResolvedClassName()))->disableOriginalConstructor()->getMock();
        if(($afterCallable = $demand->getAfterObjectCreateCallable()) !== null) {
            $object = $afterCallable($object, $resultItem, $demand, 'mock');
        }
        return $object;
    }

    /**
     * @param TestCase $testCase
     * @param MockDemand $mockDemand
     * @param MockResultItem $resultItem
     * @return Object
     */
    protected static function getRealClassObject(TestCase $testCase, MockDemand $mockDemand, MockResultItem $resultItem): Object
    {
        if (interface_exists($resultItem->getResolvedClassName())) {
            throw new \Exception('Interfaces cannot be Initialized. Please Alias or Mock in Demand, or set MockDemand::autoMockInterfaces(true) '."\ninterface: ". $classname .'. '."\n".'class Tree: '. "\n" . implode(" => \n", $resultItem->getParentClasses()));
        }

        $arguments = self::getClassArguments($testCase, $mockDemand, $resultItem);
        if(($beforeCallable = $mockDemand->getBeforeObjectCreateCallable()) !== null) {
            $beforeCallable($resultItem, $mockDemand, 'object');
        }
        $className = $resultItem->getResolvedClassName();
        $object = new $className(...$arguments);
        if(($afterCallable = $mockDemand->getAfterObjectCreateCallable()) !== null) {
            $object = $afterCallable($object, $resultItem, $mockDemand, 'object');
        }
        return $object;
    }

    /**
     * @param TestCase $testCase
     * @param MockDemand $mockDemand
     * @param MockResultItem $resultItem
     * @return array
     * @throws \ReflectionException
     */
    private static function getClassArguments(TestCase $testCase, MockDemand $mockDemand, MockResultItem $resultItem): array
    {
        $refClass = new \ReflectionClass($resultItem->getResolvedClassName());
        $refConstructor = $refClass->getConstructor();

        if ($refConstructor === null) {
            return [];
        }

        $arg = [];
        foreach($refConstructor->getParameters() AS $refArgument) {
            $refArgumentClass = $refArgument->getClass();
            if ($refArgumentClass === null) {
                throw new \Exception('Scalar Arguments in Constructor are not Supported. Please Alias or Mock in Demand '."\ninterface: ". $resultItem->getClassName() .'. '."\n".'class Tree: '. "\n" . implode(" => \n", $resultItem->getParentClasses()));
            }
            $refArgumentClassName = $refArgumentClass->getName();
            if (!empty($refArgumentClassName)) {
                $childResultItem = new MockResultItem($refArgumentClassName, $resultItem);
                $childResultItem->setObject(self::initWithMockDemand($testCase, $mockDemand, $childResultItem));
                $arg[] = $childResultItem->getObject();
            } else {
                throw new \Exception('Scalar Arguments in Constructor are not Supported. Please Alias or Mock in Demand '."\ninterface: ". $refArgumentClassName .'. '."\n".'class Tree: '. "\n" . implode(" => \n", $resultItem->getParentClasses()));
            }
        }

        return $arg;
    }
}