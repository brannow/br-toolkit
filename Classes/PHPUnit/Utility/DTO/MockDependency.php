<?php

namespace BR\Toolkit\PHPUnit\Utility\DTO;

class MockDependency
{
    public const MOCK_ROOT_ANY = '_ANY';
    public const MOCK_ROOT_PARENT = '_PARENT';
    public const MOCK_ROOT_STRICT = '_STRICT';
    private string $className;
    private array $parentStack = [];
    private string $type = self::MOCK_ROOT_ANY;
    private string $aliasClassName = '';

    /**
     * @param string $className
     */
    public function __construct(string $className)
    {
        $this->className = $className;
    }

    public function setParentClass(string ...$parentClass): void
    {
        $this->type = self::MOCK_ROOT_PARENT;
        $this->parentStack = $parentClass;
    }

    public function setStrictParentClass(string ...$parentClass): void
    {
        $this->type = self::MOCK_ROOT_STRICT;
        $this->parentStack = $parentClass;
    }

    /**
     * @return string
     */
    public function getAliasClassName(): string
    {
        return $this->aliasClassName;
    }

    /**
     * @return bool
     */
    public function aliasExists(): bool
    {
        return $this->aliasClassName !== '';
    }

    /**
     * @param string $aliasClassName
     */
    public function useAliasClassName(string $aliasClassName): void
    {
        $this->aliasClassName = $aliasClassName;
    }

    /**
     * @return string
     */
    public function generateId(): string
    {
        return md5(serialize([$this->className, $this->parentStack, $this->type]));
    }

    /**
     * @return string
     */
    public function getClassName(): string
    {
        return $this->className;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    public function match(MockResultItem $item): bool
    {
        if ($this->getClassName() === $item->getClassName()) {
            // check if special match otherwise sime
            if($this->getType() === self::MOCK_ROOT_STRICT || $this->getType() === self::MOCK_ROOT_PARENT) {
                $itemParents = $item->getParentClasses();
                $pStack = $this->parentStack;
                $pStack[] = $this->getClassName();
                if ($this->getType() === self::MOCK_ROOT_PARENT)
                    $itemParents = array_intersect($itemParents, $pStack);

                return !array_diff($itemParents, $pStack);
            }

            return true;
        }

        return false;
    }
}