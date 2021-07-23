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
     * @throws \TYPO3\CMS\Extbase\Object\Exception
     */
    public static function get(string $className, ...$arguments)
    {
        if (defined('TYPO3_branch') && strpos(TYPO3_branch, '9') === 0) {
            /** @var ObjectManager $om */
            $om = GeneralUtility::makeInstance(ObjectManager::class);
            return $om->get($className, ...$arguments);
        } else {
            return GeneralUtility::makeInstance($className, ...$arguments);
        }
    }
}