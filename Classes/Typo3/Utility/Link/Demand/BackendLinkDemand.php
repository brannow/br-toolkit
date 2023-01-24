<?php
declare(strict_types=1);
namespace BR\Toolkit\Typo3\Utility\Link\Demand;

use BR\Toolkit\Exceptions\CacheException;
use BR\Toolkit\Typo3\Utility\LinkUtility;
use TYPO3\CMS\Backend\Routing\Exception\RouteNotFoundException;

/*
 * $demand = new BackendLinkDemand();
 * $demand->setModule('web_MyModuleName');
 * $link = LinkUtility::getLink($demand);
 */
class BackendLinkDemand extends AbstractLinkDemand implements BackendLinkDemandInterface
{
    private int $uid = 0;
    private string $table = '';
    private string $module = '';
    private array $defaultValues = [];
    private array $moduleConfig = [];
    private array $returnModuleConfig = [];
    private string $returnModule = '';

    public function getContext(): string
    {
       return 'backend_' . $this->getModule();
    }

    protected function getCacheIdentifier(): array
    {
        return [
            $this->getUid(),
            $this->getLanguageId(),
            $this->getTable(),
            $this->getModule(),
            $this->getModuleConfig(),
            $this->getReturnModule(),
            $this->getDefaultValues(),
            $this->getReturnModuleConfig()
        ];
    }

    /**
     * @param array $config
     * @return $this
     */
    public function setReturnModuleConfig(array $config): BackendLinkDemandInterface
    {
        $this->returnModuleConfig = $config;
        return $this;
    }

    /**
     * @param string $module
     * @return $this
     */
    public function setReturnModule(string $module): BackendLinkDemandInterface
    {
        $this->returnModule = $module;
        return $this;
    }

    public function setUid(int $uid): BackendLinkDemandInterface
    {
        $this->uid = $uid;
        return $this;
    }

    public function setTable(string $table): BackendLinkDemandInterface
    {
        $this->table = $table;
        return $this;
    }

    public function setModule(string $module): BackendLinkDemandInterface
    {
        $this->module = $module;
        return $this;
    }

    public function setDefaultValues(array $values): BackendLinkDemandInterface
    {
        $this->defaultValues = $values;
        return $this;
    }

    public function setModuleConfig(array $config): BackendLinkDemandInterface
    {
        $this->moduleConfig = $config;
        return $this;
    }

    public function getUid(): int
    {
        return $this->uid;
    }

    public function getTable(): string
    {
       return $this->table;
    }

    public function getReturnModule(): string
    {
        return $this->returnModule;
    }

    public function getDefaultValues(): array
    {
        return $this->defaultValues;
    }

    public function getReturnModuleConfig(): array
    {
        return $this->returnModuleConfig;
    }

    public function getModule(): string
    {
        return $this->module;
    }

    /**
     * @return string[]
     */
    public function getModuleConfig(): array
    {
        return $this->moduleConfig;
    }

    /**
     * @return string
     * @throws CacheException|RouteNotFoundException
     */
    protected function getReturnUrl(): string
    {
        return (string)LinkUtility::getLink(
            (new BackendLinkDemand())
                ->setModule($this->getReturnModule())
                ->setModuleConfig($this->getReturnModuleConfig())
        );
    }
}