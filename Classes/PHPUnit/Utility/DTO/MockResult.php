<?php

namespace BR\Toolkit\PHPUnit\Utility\DTO;

class MockResult
{
    private ?MockResultItem $rootItem = null;

    public function setRootResultItem(MockResultItem $item): void
    {
        $this->rootItem = $item;
    }
}