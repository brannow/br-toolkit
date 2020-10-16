<?php


namespace BR\Toolkit\Misc\DTO\TreeProcessor;


interface TreeProcessorResultItemInterface
{
    /**
     * @param mixed $data
     */
    public function setData($data): void;

    /**
     * @return mixed
     */
    public function getData();

    /**
     * @param TreeProcessorResultItemInterface ...$child
     */
    public function addChild(TreeProcessorResultItemInterface ...$child): void;

    /**
     * @return TreeProcessorResultItemInterface[]
     */
    public function getChildren(): array;

    /**
     * @param TreeProcessorResultItemInterface|null $parent
     */
    public function setParent(?TreeProcessorResultItemInterface $parent): void;

    /**
     * @return TreeProcessorResultItemInterface|null
     */
    public function getParent(): ?TreeProcessorResultItemInterface;
}