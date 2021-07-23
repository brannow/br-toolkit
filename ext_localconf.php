<?php
if (!defined('TYPO3_MODE')) {
    die('Access denied');
}
call_user_func(function ($extKey) {

    \BR\Toolkit\Typo3\Cache\CacheManager::announceCache();

}, 'br_toolkit');
