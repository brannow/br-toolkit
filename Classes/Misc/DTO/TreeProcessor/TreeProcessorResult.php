<?php

namespace BR\Toolkit\Misc\DTO\TreeProcessor;


use ArrayIterator;
use Traversable;

class TreeProcessorResult implements TreeProcessorResultGenerateInterface
{
    /**
     * @var TreeProcessorResultItemInterface[]
     */
    private array $list = [];

    /**
     * @return TreeProcessorResultItemInterface[]
     */
    public function getRootItems(): array
    {
        $rootList = [];
        foreach ($this->list as $resultItem) {
            if ($resultItem->getParent() === null) {
                $rootList[] = $resultItem;
            }
        }
        return $rootList;
    }

    protected function getNewItemObject(): TreeProcessorResultItemInterface
    {
        return new TreeProcessorResultItem();
    }

    /**
     * @param int $id
     * @param bool $createIfNotExists
     * @return TreeProcessorResultItemInterface|null
     */
    public function getItem(int $id, bool $createIfNotExists = false): ?TreeProcessorResultItemInterface
    {
        if ($createIfNotExists) {
            $item = $this->list[$id] ?? $this->getNewItemObject();
            $this->list[$id] = $item;
            return $item;
        }

        return $this->list[$id] ?? null;
    }

    /**
     * @return int
     */
    public function count(): int
    {
        return count($this->list);
    }

    /**
     * @param TreeProcessorDataInterface $data
     * @param mixed $item
     */
    public function processData(TreeProcessorDataInterface $data, $item): void
    {
        if (($id = $data->getPrimaryIdFromData($item)) <= 0) {
            return;
        }

        // set data
        $itemObj = $this->setItemData($id, $data, $item);

        // create relation
        if (($rid = $data->getRelationIdFromData($item)) > 0) {
            $parentItem = $this->getItem($rid, true);
            // be aware this will create a cyclic object references structure
            $parentItem->addChild($itemObj);
        }
    }

    /**
     * @param int $id
     * @param TreeProcessorDataInterface $data
     * @param mixed $item
     * @return TreeProcessorResultItemInterface
     */
    protected function setItemData(int $id, TreeProcessorDataInterface $data, $item): TreeProcessorResultItemInterface
    {
        $itemObj = $this->list[$id] ??  $this->getNewItemObject();
        $this->list[$id] = $itemObj;
        $itemObj->setData($item);
        return $itemObj;
    }

    /**
     * @return TreeProcessorResultItemInterface[]
     */
    public function getItems(): array
    {
        return $this->list;
    }
}
