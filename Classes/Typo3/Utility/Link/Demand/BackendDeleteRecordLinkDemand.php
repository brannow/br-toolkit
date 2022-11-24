<?php
declare(strict_types=1);
namespace BR\Toolkit\Typo3\Utility\Link\Demand;

use BR\Toolkit\Exceptions\CacheException;
use TYPO3\CMS\Backend\Routing\Exception\RouteNotFoundException;

/*
 * $demand = new BackendDeleteRecordLinkDemand();
 * $demand->setReturnModule('web_MyModuleName');
 * $demand->setTable('tx_database_table_name');
 * $demand->setUid(1234);
 * $link = LinkUtility::getLink($demand);
 */
class BackendDeleteRecordLinkDemand extends BackendLinkDemand
{
    public function getModule(): string
    {
        return BackendLinkDemandInterface::MODULE_TCE;
    }

    /**
     * @return string[]
     * @throws CacheException|RouteNotFoundException
     */
    public function getModuleConfig(): array
    {
        return [
            'prErr' => '1',
            'uPT' => '1',
            'cmd['.$this->getTable().']['. $this->getUid() .'][delete]' => 1,
            'redirect' => $this->getReturnUrl()
        ];
    }
}