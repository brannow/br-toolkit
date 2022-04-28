<?php

namespace BR\Toolkit\Typo3\VersionWrapper;

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Object\ObjectManager;

abstract class InstanceUtility
{
    /**
     * Typo3 9 to 10 wrapper to handle the new autowiring feature better
     *
     * @param string $className
     * @param ...$arguments
     * @return object|\Psr\Log\LoggerAwareInterface|\TYPO3\CMS\Core\SingletonInterface
     */
    public static function get(string $className, ...$arguments)
    {
        return GeneralUtility::makeInstance($className, ...$arguments);
    }
}