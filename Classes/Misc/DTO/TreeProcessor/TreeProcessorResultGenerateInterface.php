<?php

namespace BR\Toolkit\Misc\DTO\TreeProcessor;


interface TreeProcessorResultGenerateInterface extends TreeProcessorResultInterface
{
    /**
     * @param TreeProcessorDataInterface $data
     * @param mixed $item
     */
    public function processData(TreeProcessorDataInterface $data, $item): void;

    /**
     * @param int $id
     * @param bool $createIfNotExists
     * @return TreeProcessorResultItemInterface|null
     */
    public function getItem(int $id, bool $createIfNotExists = false): ?TreeProcessorResultItemInterface;
}