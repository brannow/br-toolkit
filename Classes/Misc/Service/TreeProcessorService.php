<?php


namespace BR\Toolkit\Misc\Service;

use BR\Toolkit\Misc\DTO\TreeProcessor\TreeProcessorDataInterface;
use BR\Toolkit\Misc\DTO\TreeProcessor\TreeProcessorResultInterface;

class TreeProcessorService
{
    /**
     * @deprecated use TreeProcessorArrayData->getResult() direct
     * @param TreeProcessorDataInterface $data
     * @return TreeProcessorResultInterface
     */
    public function processTreeResult(TreeProcessorDataInterface $data): TreeProcessorResultInterface
    {
        return $data->getResult();
    }
}