<?php

namespace BR\Toolkit\Typo3\Utility;

use BR\Toolkit\Exceptions\Typo3ConfigException;
use BR\Toolkit\Typo3\Utility\Stud\FakeMiddlewareHandler;
use BR\Toolkit\Typo3\VersionWrapper\InstanceUtility;
use TYPO3\CMS\Core\Cache\Backend\NullBackend;
use TYPO3\CMS\Core\Cache\CacheManager;
use TYPO3\CMS\Core\Cache\Frontend\FrontendInterface;
use TYPO3\CMS\Core\Cache\Frontend\NullFrontend;
use TYPO3\CMS\Core\Cache\Frontend\VariableFrontend;
use TYPO3\CMS\Core\Context\Context;
use TYPO3\CMS\Core\Exception\SiteNotFoundException;
use TYPO3\CMS\Core\Http\ServerRequestFactory;
use TYPO3\CMS\Core\Routing\PageArguments;
use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Core\Site\Entity\Site;
use TYPO3\CMS\Core\Site\Entity\SiteLanguage;
use TYPO3\CMS\Core\Site\SiteFinder;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManager;
use TYPO3\CMS\Extbase\Mvc\Web\Routing\UriBuilder;
use TYPO3\CMS\Extbase\Service\EnvironmentService;
use TYPO3\CMS\Extbase\Service\ExtensionService;
use TYPO3\CMS\Frontend\Authentication\FrontendUserAuthentication;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;
use TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController;
use TYPO3\CMS\Frontend\Middleware\TypoScriptFrontendInitialization;

abstract class FrontendUtility
{
    /**
     * @var UriBuilder|null
     */
    protected static $uriBuilder = null;

    /**
     * @param int $pageId
     * @param int $langId
     * @param int $type
     * @return TypoScriptFrontendController|null
     * @throws Typo3ConfigException
     * @throws \TYPO3\CMS\Core\Error\Http\InternalServerErrorException
     * @throws \TYPO3\CMS\Core\Error\Http\ServiceUnavailableException
     */
    public static function getFrontendControllerWithPageId(int $pageId, int $langId = 0, int $type = 0): ?TypoScriptFrontendController
    {
        $site = static::getSite($pageId);
        if ($langId > 0) {
            $lang = $site->getLanguageById($langId);
        } else {
            $lang = $site->getDefaultLanguage();
        }

        return static::getFrontendController($site, $lang, $type);
    }

    /**
     * @param Site|null $site
     * @param SiteLanguage|null $siteLanguage
     * @param int $type
     * @return TypoScriptFrontendController|null
     * @throws Typo3ConfigException
     * @throws \TYPO3\CMS\Core\Error\Http\InternalServerErrorException
     * @throws \TYPO3\CMS\Core\Error\Http\ServiceUnavailableException
     */
    public static function getFrontendController(?Site $site = null, ?SiteLanguage $siteLanguage = null, int $type = 0): ?TypoScriptFrontendController
    {
        if (isset($GLOBALS['TSFE']) && $GLOBALS['TSFE'] instanceof TypoScriptFrontendController) {
            // only use the current existing tsfe if the site lang is correct
            if ($siteLanguage === null || $GLOBALS['TSFE']->getLanguage()->getLanguageId() === $siteLanguage->getLanguageId()) {
                return $GLOBALS['TSFE'];
            }
        }

        if ($site === null) {
            $site = static::getSite();
        }

        if ($siteLanguage === null) {
            // find default language in config
            $siteLanguage =  $site->getDefaultLanguage();
        }

        // cli mode check for base url
        if (static::isCLi() || static::isInvalidRequestUrl()) {
            GeneralUtility::setIndpEnv('TYPO3_SSL', $site->getBase()->getScheme() === 'https');
            GeneralUtility::setIndpEnv('HTTP_HOST', $site->getBase()->getHost());
            GeneralUtility::setIndpEnv('REQUEST_URI', $site->getBase()->getPath());
            GeneralUtility::setIndpEnv('TYPO3_REQUEST_HOST', $site->getBase()->getScheme().'://'.$site->getBase()->getHost());
            $base = (string)$site->getBase();
            GeneralUtility::setIndpEnv('TYPO3_REQUEST_URL', $base);
            GeneralUtility::setIndpEnv('TYPO3_SITE_URL', $base);
            GeneralUtility::setIndpEnv('REMOTE_ADDR', '127.0.0.1');
            GeneralUtility::setIndpEnv('HTTP_USER_AGENT', 'Custom - mode');
        }

        $request = $GLOBALS['TYPO3_REQUEST'] ?? ServerRequestFactory::fromGlobals();
        $frontendUser = InstanceUtility::get(FrontendUserAuthentication::class);
        $frontendUser->start();
        $frontendUser->unpack_uc();
        $pageArguments = InstanceUtility::get(PageArguments::class, $site->getRootPageId(), $type, [], [], $request->getQueryParams());
        $request = $request->withAttribute('language', $siteLanguage)
            ->withAttribute('site', $site)
            ->withAttribute('frontend.user', $frontendUser)
            ->withAttribute('routing', $pageArguments);

        /** @var FrontendInterface $nullFrontend */
        if (class_exists(NullFrontend::class)) {
            $nullFrontend = InstanceUtility::get(NullFrontend::class, 'pages');
        } else {
            $_GET['id'] = 1;
            $_GET['type'] = $type;
            $nullFrontend = InstanceUtility::get(
                VariableFrontend::class,
                'pages',
                InstanceUtility::get(NullBackend::class, 'nullContext')
            );

        }
        $cacheManager = InstanceUtility::get(CacheManager::class);
        try {
            $cacheManager->registerCache($nullFrontend);
        } catch (\Exception $exception) {
            unset($exception);
        }

        $tsfeInit = InstanceUtility::get(TypoScriptFrontendInitialization::class);
        $tsfeInit->process($request, new FakeMiddlewareHandler());
        $GLOBALS['TYPO3_REQUEST'] = $request;
        $GLOBALS['TSFE']->sys_page = InstanceUtility::get(\TYPO3\CMS\Frontend\Page\PageRepository::class);
        $GLOBALS['TSFE']->getPageAndRootlineWithDomain(1, $request);
        $GLOBALS['TSFE']->fe_user = $frontendUser;
        $GLOBALS['TSFE']->getConfigArray($request);
        $GLOBALS['TSFE']->tmpl->start($GLOBALS['TSFE']->rootLine);

        // Locks may be acquired here
        $GLOBALS['TSFE']->getFromCache($request);
        // Get config if not already gotten
        // After this, we should have a valid config-array ready
        $GLOBALS['TSFE']->getConfigArray($request);
        // Setting language and locale
        $GLOBALS['TSFE']->settingLanguage($request);

        return $GLOBALS['TSFE'];
    }

    /**
     * @return bool
     */
    protected static function isCLi(): bool
    {
        return php_sapi_name() === 'cli';
    }

    /**
     * @return bool
     */
    protected static function isInvalidRequestUrl(): bool
    {
        return parse_url(GeneralUtility::getIndpEnv('TYPO3_REQUEST_URL')) === false;
    }

    /**
     * @param int $pageId
     * @return Site
     * @throws Typo3ConfigException
     */
    public static function getSite(int $pageId = -1): Site
    {
        /** @var SiteFinder $siteFinder */
        $siteFinder = InstanceUtility::get(SiteFinder::class);
        try {
            return $siteFinder->getSiteByPageId($pageId);
        } catch (SiteNotFoundException $e) {
            foreach ($siteFinder->getAllSites() as $site) {
                return $site;
            }
        }

        if ($pageId > -1) {
            throw new Typo3ConfigException('missing site config for page \''. $pageId .'\'');
        } else {
            throw new Typo3ConfigException('missing site config');
        }
    }

    /**
     * @param Site|null $site
     * @param SiteLanguage|null $siteLanguage
     * @param int $type
     * @return UriBuilder
     * @throws Typo3ConfigException
     * @throws \TYPO3\CMS\Core\Error\Http\InternalServerErrorException
     * @throws \TYPO3\CMS\Core\Error\Http\ServiceUnavailableException
     */
    public static function getUriBuilder(?Site $site = null, ?SiteLanguage $siteLanguage = null, int $type = 0): UriBuilder
    {
        if (static::$uriBuilder === null) {
            static::getFrontendController($site, $siteLanguage, $type);
            /** @var UriBuilder $uriBuilder */
            $uriBuilder = InstanceUtility::get(UriBuilder::class);
            /** @var ContentObjectRenderer $contentObjectRenderer */
            $contentObjectRenderer = InstanceUtility::get(ContentObjectRenderer::class);
            /** @var ConfigurationManager $configurationManager */
            $configurationManager = InstanceUtility::get(ConfigurationManager::class);
            /** @var EnvironmentService $envService */
            $envService = InstanceUtility::get(EnvironmentService::class);
            /** @var ExtensionService $extService */
            $extService = InstanceUtility::get(ExtensionService::class);
            $configurationManager->setContentObject($contentObjectRenderer);
            $uriBuilder->injectEnvironmentService($envService);
            $uriBuilder->injectConfigurationManager($configurationManager);
            $uriBuilder->injectExtensionService($extService);
            $uriBuilder->initializeObject();
            static::$uriBuilder = $uriBuilder;
        }

        return static::$uriBuilder->reset();
    }
}