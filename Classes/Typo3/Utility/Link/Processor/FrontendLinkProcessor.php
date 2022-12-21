<?php
declare(strict_types=1);
namespace BR\Toolkit\Typo3\Utility\Link\Processor;


use BR\Toolkit\Exceptions\Typo3ConfigException;
use BR\Toolkit\Typo3\Utility\FrontendUtility;
use BR\Toolkit\Typo3\Utility\Link\Demand\FrontendLinkDemandInterface;
use BR\Toolkit\Typo3\Utility\Link\Demand\FrontendPluginLinkDemandInterface;
use BR\Toolkit\Typo3\Utility\Link\Demand\LinkDemandInterface;
use TYPO3\CMS\Core\Error\Http\InternalServerErrorException;
use TYPO3\CMS\Core\Error\Http\ServiceUnavailableException;
use TYPO3\CMS\Extbase\Mvc\Web\Routing\UriBuilder;

class FrontendLinkProcessor extends AbstractLinkProcessor
{
    private static ?UriBuilder $uriBuilder = null;

    public static function supportDemand(LinkDemandInterface $demand): bool
    {
        return ($demand instanceof FrontendLinkDemandInterface);
    }

    /**
     * @param LinkDemandInterface|FrontendLinkDemandInterface|FrontendPluginLinkDemandInterface $demand
     * @return string
     * @throws InternalServerErrorException|ServiceUnavailableException|Typo3ConfigException
     */
    public function process(LinkDemandInterface|FrontendLinkDemandInterface|FrontendPluginLinkDemandInterface $demand): string
    {
        $builder = $this->getUriBuilder();
        foreach ($demand->getBuilderMapping() as $method => $value) {
            $builder->{$method}($value);
        }

        if ($demand instanceof FrontendPluginLinkDemandInterface) {
            return $builder->uriFor(...$demand->getNamedPluginConfig());
        }

        return $builder->buildFrontendUri();
    }

    /**
     * @return UriBuilder
     * @throws Typo3ConfigException
     * @throws InternalServerErrorException
     * @throws ServiceUnavailableException
     */
    private function getUriBuilder(): UriBuilder
    {
        return self::$uriBuilder ?? (self::$uriBuilder = FrontendUtility::getUriBuilder());
    }
}