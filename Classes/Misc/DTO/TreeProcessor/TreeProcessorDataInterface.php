<?php


namespace BR\Toolkit\Misc\DTO\TreeProcessor;


interface TreeProcessorDataInterface
{
    /**
     * @param mixed $data
     * @return int
     */
    public function getPrimaryIdFromData($data): int;

    /**
     * @param mixed $data
     * @return int
     */
    public function getRelationIdFromData($data): int;

    /**
     * @return \Iterator|array
     */
    public function getData();

    /**
     * @return TreeProcessorResultInterface
     */
    public function getResult(): TreeProcessorResultInterface;
}