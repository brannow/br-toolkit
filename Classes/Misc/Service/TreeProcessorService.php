<?php


namespace BR\Toolkit\Misc\Service;

use BR\Toolkit\Exceptions\ServiceException;
use BR\Toolkit\Misc\DTO\TreeProcessor\TreeProcessorDataInterface;
use BR\Toolkit\Misc\DTO\TreeProcessor\TreeProcessorResult;
use BR\Toolkit\Misc\DTO\TreeProcessor\TreeProcessorResultGenerateInterface;
use BR\Toolkit\Misc\DTO\TreeProcessor\TreeProcessorResultInterface;

class TreeProcessorService
{
    /**
     * @param TreeProcessorDataInterface $data
     * @return TreeProcessorResultInterface
     */
    public function processTreeResult(TreeProcessorDataInterface $data): TreeProcessorResultInterface
    {
        return $this->generateTreeResult($data);
    }

    /**
     * overwrite to replace result object
     * @return TreeProcessorResultGenerateInterface
     */
    protected function createTreeResult(): TreeProcessorResultGenerateInterface
    {
        return new TreeProcessorResult();
    }

    /**
     * @param TreeProcessorDataInterface $data
     * @return TreeProcessorResultInterface
     */
    private function generateTreeResult(TreeProcessorDataInterface $data): TreeProcessorResultInterface
    {
        $tree = $this->createTreeResult();
        foreach ($data->getData() as $itemData) {
            $id = $data->getPrimaryIdFromData($itemData);
            $rid = $data->getRelationIdFromData($itemData);

            if ($id <= 0) {
                continue;
            }

            // init/set primary treeItem data
            $item = $tree->setItemData($id, $itemData);

            // create relation
            if ($rid > 0) {
                $parentItem = $tree->getItem($rid, true);
                // be aware this will create a cyclic object references structure
                $parentItem->addChild($item);
            }
        }

        return $tree;
    }
}