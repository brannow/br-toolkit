<?php

namespace BR\Toolkit\PHPUnit\Utility\DTO;

class MockResult
{
    private ?MockResultItem $rootItem = null;

    public function getObject(): ?object
    {
        if ($this->rootItem)
            return $this->rootItem->getObject();

        return null;
    }

    public function getResultItem(): ?MockResultItem
    {
        return $this->rootItem;
    }

    public function setRootResultItem(MockResultItem $item): void
    {
        $this->rootItem = $item;
    }
}