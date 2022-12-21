<?php

namespace BR\Toolkit\Typo3\Utility\Link\Processor;

use BR\Toolkit\Exceptions\CacheException;
use BR\Toolkit\Typo3\Utility\Link\Demand\LinkDemandInterface;
use Exception;
use TYPO3\CMS\Backend\Routing\Exception\RouteNotFoundException;

interface LinkProcessorInterface
{
    public static function supportDemand(LinkDemandInterface $demand): bool;

    /**
     * @param LinkDemandInterface $demand
     * @return string
     * @throws CacheException|RouteNotFoundException|Exception
     */
    public function process(LinkDemandInterface $demand): string;
    public function postProcessLink(string $link): string;
}