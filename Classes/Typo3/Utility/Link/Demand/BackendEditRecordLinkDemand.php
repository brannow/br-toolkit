<?php
declare(strict_types=1);
namespace BR\Toolkit\Typo3\Utility\Link\Demand;

use BR\Toolkit\Exceptions\CacheException;
use TYPO3\CMS\Backend\Routing\Exception\RouteNotFoundException;

/*
 * $demand = new BackendEditRecordLinkDemand();
 * $demand->setReturnModule('web_MyModuleName');
 * $demand->setTable('tx_database_table_name');
 * $demand->setUid(1234);
 * $link = LinkUtility::getLink($demand);
 */
class BackendEditRecordLinkDemand extends BackendLinkDemand
{
    public function getModule(): string
    {
        return BackendLinkDemandInterface::MODULE_RECORD;
    }

    /**
     * @return string[]
     * @throws CacheException|RouteNotFoundException
     */
    public function getModuleConfig(): array
    {
        $param = [
            'edit['. $this->getTable() .']['. $this->getUid() .']' => 'edit',
            'returnUrl' => $this->getReturnUrl()
        ];

        if ($this->getLanguageId() > 0) {
            $param['justLocalized'] = $this->getTable().':'.$this->getUid().':'.$this->getLanguageId();
        }

        if (!empty($this->getDefaultValues())) {
            foreach ($this->getDefaultValues() as $key => $value) {
                $param['defVals['. $this->getTable() .']['. $key .']'] = $value;
            }
        }

        return $param;
    }
}