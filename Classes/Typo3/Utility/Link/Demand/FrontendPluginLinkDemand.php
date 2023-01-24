<?php
declare(strict_types=1);
namespace BR\Toolkit\Typo3\Utility\Link\Demand;

/**
 * $demand = new FrontendPluginLinkDemand();
 * $demand->setActionName('list');
 * $demand->setControllerName('Event');
 * $link = LinkUtility::getLink($demand);
 */
class FrontendPluginLinkDemand extends FrontendLinkDemand implements FrontendPluginLinkDemandInterface
{
    private string $actionName = '';
    private array $controllerArguments = [];
    private string $controllerName = '';
    private string $extensionName = '';
    private string $pluginName = '';

    /**
     * @return array
     */
    public function getNamedPluginConfig(): array
    {
        return array_filter([
            'actionName' => $this->getActionName(),
            'controllerArguments' => $this->getControllerArguments(),
            'controllerName' => $this->getControllerName(),
            'extensionName' => $this->getExtensionName(),
            'pluginName' => $this->getPluginName()
        ]);
    }

    protected function getCacheIdentifier(): array
    {
        return [...parent::getCacheIdentifier(), ...$this->getNamedPluginConfig()];
    }

    /**
     * @return string
     */
    public function getActionName(): string
    {
        return $this->actionName;
    }

    /**
     * @param string $actionName
     * @return $this
     */
    public function setActionName(string $actionName): FrontendPluginLinkDemandInterface
    {
        $this->actionName = $actionName;
        return $this;
    }

    /**
     * @return array
     */
    public function getControllerArguments(): array
    {
        return $this->controllerArguments;
    }

    /**
     * @param array $controllerArguments
     * @return $this
     */
    public function setControllerArguments(array $controllerArguments): FrontendPluginLinkDemandInterface
    {
        $this->controllerArguments = $controllerArguments;
        return $this;
    }

    /**
     * @return string
     */
    public function getControllerName(): string
    {
        return $this->controllerName;
    }

    /**
     * @param string $controllerName
     * @return $this
     */
    public function setControllerName(string $controllerName): FrontendPluginLinkDemandInterface
    {
        $this->controllerName = $controllerName;
        return $this;
    }

    /**
     * @return string
     */
    public function getExtensionName(): string
    {
        return $this->extensionName;
    }

    /**
     * @param string $extensionName
     * @return $this
     */
    public function setExtensionName(string $extensionName): FrontendPluginLinkDemandInterface
    {
        $this->extensionName = $extensionName;
        return $this;
    }

    /**
     * @return string
     */
    public function getPluginName(): string
    {
        return $this->pluginName;
    }

    /**
     * @param string $pluginName
     * @return $this
     */
    public function setPluginName(string $pluginName): FrontendPluginLinkDemandInterface
    {
        $this->pluginName = $pluginName;
        return $this;
    }
}