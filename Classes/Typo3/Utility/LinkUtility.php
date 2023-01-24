<?php
declare(strict_types=1);
namespace BR\Toolkit\Typo3\Utility;

use BR\Toolkit\Exceptions\CacheException;
use BR\Toolkit\Typo3\Cache\CacheService;
use BR\Toolkit\Typo3\Utility\Link\Demand\BackendLinkDemandInterface;
use BR\Toolkit\Typo3\Utility\Link\Demand\FrontendLinkDemandInterface;
use BR\Toolkit\Typo3\Utility\Link\Demand\LinkDemandInterface;
use BR\Toolkit\Typo3\Utility\Link\Demand\LinkResult;
use BR\Toolkit\Typo3\Utility\Link\Demand\LinkResultInterface;
use BR\Toolkit\Typo3\Utility\Link\Processor\AbstractLinkProcessor;
use BR\Toolkit\Typo3\Utility\Link\Processor\BackendLinkProcessor;
use BR\Toolkit\Typo3\Utility\Link\Processor\FrontendLinkProcessor;
use BR\Toolkit\Typo3\Utility\Link\Processor\LinkProcessorInterface;
use Exception;
use TYPO3\CMS\Backend\Routing\Exception\RouteNotFoundException;
use TYPO3\CMS\Core\Utility\GeneralUtility;

abstract class LinkUtility
{
    private static array $runtimeCache = [];
    private static ?CacheService $cacheService = null;

    /**
     * @param FrontendLinkDemandInterface|BackendLinkDemandInterface $linkDemand
     * @return LinkResultInterface
     * @throws CacheException|RouteNotFoundException
     */
    public static function getLink(LinkDemandInterface $linkDemand): LinkResultInterface
    {
        return self::getCacheLink($linkDemand);
    }

    /**
     * @param FrontendLinkDemandInterface|BackendLinkDemandInterface $linkDemand
     * @return LinkResultInterface
     * @throws CacheException|RouteNotFoundException|Exception
     */
    private static function getCacheLink(LinkDemandInterface $linkDemand): LinkResultInterface
    {
        $processor = static::getProcessor($linkDemand);

        if ($linkDemand->getCacheLevel() === LinkDemandInterface::CACHE_LEVEL_PERSIST) {
            $link = self::getCacheService()->cache(
                'link_generator_' . $linkDemand->getCacheKey(),
                fn() => $processor->process($linkDemand),
                $linkDemand->getContext() . '_link',
                $linkDemand->getCacheLifeTime()
            );
        } elseif ($linkDemand->getCacheLevel() === LinkDemandInterface::CACHE_LEVEL_RUNTIME) {
            $cacheKey = $linkDemand->getCacheKey();
            $link = self::$runtimeCache[$cacheKey] ?? (self::$runtimeCache[$cacheKey] = $processor->process($linkDemand));
        } else {
            $link = $processor->process($linkDemand);
        }

        return (new LinkResult())
            ->setLink($processor->postProcessLink($link))
            ->setDemand($linkDemand);
    }

    /**
     * @param FrontendLinkDemandInterface|BackendLinkDemandInterface $linkDemand
     * @return LinkProcessorInterface
     * @throws Exception
     */
    protected static function getProcessor(LinkDemandInterface $linkDemand): LinkProcessorInterface
    {
        $processor = AbstractLinkProcessor::getSupportedProcessor($linkDemand);
        if ($processor === null) {
            throw new Exception('Link processor not defined for demand type \'' . get_class($linkDemand) . '\'');
        }

        return $processor;
    }

    /**
     * @return CacheService
     */
    protected static function getCacheService(): CacheService
    {
        return self::$cacheService ?? (self::$cacheService = GeneralUtility::makeInstance(CacheService::class));
    }
}