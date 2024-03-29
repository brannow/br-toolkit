<?php
declare(strict_types=1);
namespace BR\Toolkit\Typo3\Utility\Link\Demand;

use BR\Toolkit\Exceptions\CacheException;
use TYPO3\CMS\Backend\Routing\Exception\RouteNotFoundException;

interface BackendLinkDemandInterface extends LinkDemandInterface
{
    public const MODULE_RECORD = 'record_edit';
    public const MODULE_TCE = 'tce_db';

    public const TYPE_EDIT = 'edit';
    public const TYPE_CREATE = 'create';
    public const TYPE_DELETE = 'delete';
    public const TYPE_LOCALIZE = 'localize';
    public const TYPE_RETURN = 'return';

    public function setUid(int $uid): BackendLinkDemandInterface;
    public function setTable(string $table): BackendLinkDemandInterface;
    public function setModule(string $module): BackendLinkDemandInterface;
    public function setModuleConfig(array $config): BackendLinkDemandInterface;
    public function setReturnModule(string $module): BackendLinkDemandInterface;
    public function setReturnModuleConfig(array $config): BackendLinkDemandInterface;
    public function setDefaultValues(array $values): BackendLinkDemandInterface;

    public function getUid(): int;
    public function getTable(): string;
    public function getReturnModule(): string;
    public function getDefaultValues(): array;
    public function getReturnModuleConfig(): array;

    /**
     * @return string
     */
    public function getModule(): string;
    /**
     * @return array
     * @throws CacheException|RouteNotFoundException
     */
    public function getModuleConfig(): array;
}