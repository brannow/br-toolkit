<?php
declare(strict_types=1);
namespace BR\Toolkit\Typo3\Utility\Link\Demand;

interface FrontendPluginLinkDemandInterface
{
    public function getNamedPluginConfig(): array;

    public function getActionName(): string;
    public function getControllerArguments(): array;
    public function getControllerName(): string;
    public function getExtensionName(): string;
    public function getPluginName(): string;
}