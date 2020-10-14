<?php

namespace BR\Toolkit\Misc\DTO\TreeProcessor;


interface TreeProcessorResultGenerateInterface extends TreeProcessorResultInterface
{
    /**
     * @param int $id
     * @param mixed $data
     * @return TreeProcessorResultItemInterface
     */
    public function setItemData(int $id, $data): TreeProcessorResultItemInterface;

    /**
     * @param int $id
     * @param bool $createIfNotExists
     * @return TreeProcessorResultItemInterface|null
     */
    public function getItem(int $id, bool $createIfNotExists = false): ?TreeProcessorResultItemInterface;

    /**
     * @param TreeProcessorResultItemInterface $parent
     * @param TreeProcessorResultItemInterface ...$children
     */
    public function relateItems(TreeProcessorResultItemInterface $parent, TreeProcessorResultItemInterface ...$children): void;
}