<?php

namespace BR\Toolkit\PHPUnit\Utility\DTO;

class MockResultItem
{
    private ?MockResultItem $parent;
    private string $className;
    private ?Object $object = null;
    private array $children = [];
    private string $aliasClassName = '';

    /**
     * @param MockResultItem|null $parentItem
     */
    public function __construct(string $className, ?MockResultItem $parentItem = null)
    {
        $this->className = $className;
        $this->parent = $parentItem;
        if ($this->parent)
            $this->parent->addChildren($this);
    }

    /**
     * @return string
     */
    public function getAliasClassName(): string
    {
        return $this->aliasClassName;
    }

    /**
     * @param string $aliasClassName
     */
    public function setAliasClassName(string $aliasClassName): void
    {
        $this->aliasClassName = $aliasClassName;
    }

    public function addChildren(MockResultItem $children): void
    {
        $this->children[$children->getClassName()] = $children;
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
    public function getResolvedClassName(): string
    {
        return $this->getAliasClassName() === '' ? $this->getClassName() : $this->getAliasClassName();
    }

    public function setObject(Object $obj): void
    {
        $this->object = $obj;
    }

    /**
     * @return Object|null
     */
    public function getObject(): ?object
    {
        return $this->object;
    }

    /**
     * @return MockResultItem|null
     */
    public function getParentItem(): ?MockResultItem
    {
        return $this->parent;
    }

    public function getParentClasses(): array
    {
        $classList = [];
        if ($this->getParentItem() !== null) {
            $classList = $this->getParentItem()->getParentClasses();
        }
        $classList[] = $this->getClassName();
        return $classList;
    }
}