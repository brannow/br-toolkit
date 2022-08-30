<?php

namespace BR\Toolkit\Typo3\VersionWrapper;

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Object\ObjectManager;

abstract class InstanceUtility
{
    /**
     * Typo3 9 to 10 wrapper to handle the new autowiring feature better
     *
     * @template T of object
     * @param string $className
     * @param ...$arguments
     * @return T the created instance
     */
    public static function get(string $className, ...$arguments)
    {
        return GeneralUtility::makeInstance($className, ...$arguments);
    }
}