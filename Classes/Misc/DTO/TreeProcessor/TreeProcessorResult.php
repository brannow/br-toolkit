<?php

namespace BR\Toolkit\Misc\DTO\TreeProcessor;


class TreeProcessorResult implements TreeProcessorResultGenerateInterface
{
    /**
     * @var TreeProcessorResultItemInterface[]
     */
    private $list = [];

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

    /**
     * @param int $id
     * @param bool $createIfNotExists
     * @return TreeProcessorResultItemInterface|null
     */
    public function getItem(int $id, bool $createIfNotExists = false): ?TreeProcessorResultItemInterface
    {
        if ($createIfNotExists) {
            $item = $this->list[$id] ?? new TreeProcessorResultItem();
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
     * @param int $id
     * @param mixed $data
     * @return TreeProcessorResultItemInterface
     */
    public function setItemData(int $id, $data): TreeProcessorResultItemInterface
    {
        // create new item if not exists
        $item = $this->list[$id] ?? new TreeProcessorResultItem();
        $this->list[$id] = $item;
        $item->setData($data);
        return $item;
    }
}