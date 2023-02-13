<?php

namespace BR\Toolkit\PHPUnit\Utility\DTO;

class MockDemand
{
    private bool $autoMockInterfaces = false;
    private string $className;
    /** @var MockDependency[] */
    private array $mockDependencyList = [];

    /**
     * @var null|callable
     */
    private $beforeObjectCreateCallable = null;
    /**
     * @var null|callable
     */
    private $afterObjectCreateCallable = null;

    /**
     * @param string $className
     */
    public function __construct(string $className)
    {
        $this->className = $className;
    }

    public function autoMockInterfaces(bool $yesNo): void
    {
        $this->autoMockInterfaces = $yesNo;
    }





    /**
    $mockDemand->setBeforeObjectCreateCallable(function ($item, $demand, $type) {
        return;
    });
     *
     * @param callable|null $callback
     */
    public function setBeforeObjectCreateCallable(?callable $callback): void
    {
        $this->beforeObjectCreateCallable = $callback;
    }

    /**
    $mockDemand->setBeforeObjectCreateCallable(function ($item, $demand, $type) {
        return;
    });
     * @return callable|null
     */
    public function getBeforeObjectCreateCallable(): ?callable
    {
        return $this->beforeObjectCreateCallable;
    }

    /**
    $mockDemand->setAfterObjectCreateCallable(function ($object, $item, $demand, $type) {
        return $object;
    });
     * @return callable|null
     */
    public function getAfterObjectCreateCallable(): ?callable
    {
        return $this->afterObjectCreateCallable;
    }

    /**
    $mockDemand->setAfterObjectCreateCallable(function ($object, $item, $demand, $type) {
        return $object;
    });
     * @param callable|null $afterObjectCreateCallable
     */
    public function setAfterObjectCreateCallable(?callable $afterObjectCreateCallable): void
    {
        $this->afterObjectCreateCallable = $afterObjectCreateCallable;
    }

    /**
     * @return string
     */
    public function getClassName(): string
    {
        return $this->className;
    }

    public function addMockDependencies(string ...$dependencies): void
    {
        foreach ($dependencies as $dependency) {
            $this->addMockDependencyObject(new MockDependency($dependency));
        }
    }

    public function addMockDependencyObject(MockDependency $mockDependency)
    {
        $this->mockDependencyList[$mockDependency->getType()][$mockDependency->generateId()] = $mockDependency;
    }

    /**
     * @param MockResultItem $item
     * @return MockDependency|null
     */
    public function getMockAliasDependency(MockResultItem $item): ?MockDependency
    {
        foreach ($this->processMockDependencies($item) as $matchedMockList) {
            foreach ($matchedMockList as $mockDep) {
                if ($mockDep->aliasExists()) {
                    return $mockDep;
                }
            }
        }

        return null;
    }

    /**
     * @param MockResultItem $item
     * @return bool
     */
    public function isMockable(MockResultItem $item): bool
    {
        if ($this->autoMockInterfaces && interface_exists($item->getClassName()))
            return true;

        return !empty(array_filter($this->processMockDependencies($item)));
    }

    /**
     * @param MockResultItem $item
     * @return MockDependency[][]
     */
    private function processMockDependencies(MockResultItem $item): array
    {
        // order is important: strict > parent > all
        return [
            MockDependency::MOCK_ROOT_STRICT => $this->checkMockDependencies($item, MockDependency::MOCK_ROOT_STRICT),
            MockDependency::MOCK_ROOT_PARENT => $this->checkMockDependencies($item, MockDependency::MOCK_ROOT_PARENT),
            MockDependency::MOCK_ROOT_ANY => $this->checkMockDependencies($item, MockDependency::MOCK_ROOT_ANY)
        ];
    }

    /**
     * @param MockResultItem $item
     * @param string $type
     * @return MockDependency[]
     */
    private function checkMockDependencies(MockResultItem $item, string $type): array
    {
        /** @var MockDependency $dependency */
        $matchedDependencies = [];
        foreach ($this->mockDependencyList[$type]??[] as $dependency) {
            if ($dependency->match($item))
                $matchedDependencies[] = $dependency;
        }

        return $matchedDependencies;
    }
}