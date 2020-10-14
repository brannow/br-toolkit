<?php

namespace BR\Toolkit\Misc\DTO\TreeProcessor;


interface TreeProcessorResultInterface
{
    /**
     * @return TreeProcessorResultItemInterface[]
     */
    public function getRootItems(): array;

    /**
     * @param int $id
     * @return TreeProcessorResultItemInterface|null
     */
    public function getItem(int $id): ?TreeProcessorResultItemInterface;

    /**
     * @return int
     */
    public function count(): int;
}