<?php
defined('TYPO3') || die();

call_user_func(function ($extKey) {

    \BR\Toolkit\Typo3\Cache\CacheManager::announceCache();
    $GLOBALS['TYPO3_CONF_VARS']['SYS']['routing']['aspects']['PersistedEscapedAliasMapper'] =
        \BR\Toolkit\Typo3\Routing\Aspect\PersistedEscapedAliasMapper::class;

}, 'br_toolkit');
