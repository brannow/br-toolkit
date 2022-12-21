<?php

namespace BR\Toolkit\Typo3\Utility\Link\Processor;

use BR\Toolkit\Typo3\Utility\Link\Demand\LinkDemandInterface;
use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;

abstract class AbstractLinkProcessor implements LinkProcessorInterface, SingletonInterface
{
    protected static array $processorRegister = [
        FrontendLinkProcessor::class,
        BackendLinkProcessor::class
    ];

    public static function getSupportedProcessor(LinkDemandInterface $demand): ?LinkProcessorInterface
    {
        foreach (self::$processorRegister as $processorClass) {
            if (
                is_subclass_of($processorClass, LinkProcessorInterface::class) &&
                $processorClass::supportDemand($demand)
            ) {
                return GeneralUtility::makeInstance($processorClass);
            }
        }

        return null;
    }

    public function postProcessLink(string $link): string
    {
        return $link;
    }
}