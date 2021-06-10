<?php

namespace BR\Toolkit\Typo3\Utility;

use BR\Toolkit\Exceptions\Typo3ConfigException;
use BR\Toolkit\Typo3\VersionWrapper\InstanceUtility;
use TYPO3\CMS\Core\Cache\CacheManager;
use TYPO3\CMS\Core\Cache\Frontend\NullFrontend;
use TYPO3\CMS\Core\Context\Context;
use TYPO3\CMS\Core\Exception\SiteNotFoundException;
use TYPO3\CMS\Core\Http\ServerRequestFactory;
use TYPO3\CMS\Core\Routing\PageArguments;
use TYPO3\CMS\Core\Site\Entity\Site;
use TYPO3\CMS\Core\Site\Entity\SiteLanguage;
use TYPO3\CMS\Core\Site\SiteFinder;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManager;
use TYPO3\CMS\Extbase\Mvc\Web\Routing\UriBuilder;
use TYPO3\CMS\Extbase\Service\EnvironmentService;
use TYPO3\CMS\Extbase\Service\ExtensionService;
use TYPO3\CMS\Frontend\Authentication\FrontendUserAuthentication;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;
use TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController;

abstract class FrontendUtility
{
    /**
     * @var UriBuilder|null
     */
    protected static ?UriBuilder $uriBuilder = null;

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
        if ($GLOBALS['TSFE'] instanceof TypoScriptFrontendController) {
            // only use the current existing tsfe if the site lang is correct
            if ($siteLanguage === null || $GLOBALS['TSFE']->getLanguage()->getLanguageId() === $siteLanguage->getLanguageId()) {
                return $GLOBALS['TSFE'];
            }
        }

        if ($site === null) {
            // todo: find root page id in config
            $site = static::getSite();
        }

        if ($siteLanguage === null) {
            // find default language in config
            $siteLanguage =  $site->getDefaultLanguage();
        }

        $pageArguments = InstanceUtility::get(PageArguments::class, $site->getRootPageId(), $type, []);
        /** @var NullFrontend $nullFrontend */
        $nullFrontend = InstanceUtility::get(NullFrontend::class, 'pages');
        $cacheManager = InstanceUtility::get(CacheManager::class);
        try {
            $cacheManager->registerCache($nullFrontend);
        } catch (\Exception $exception) {
            unset($exception);
        }
        $GLOBALS['TSFE'] = new TypoScriptFrontendController(
            InstanceUtility::get(Context::class),
            $site,
            $siteLanguage,
            $pageArguments
        );

        $request = $GLOBALS['TYPO3_REQUEST'] ?? ServerRequestFactory::fromGlobals();
        $request = $request->withAttribute('language', $siteLanguage);
        $request = $request->withAttribute('site', $site);
        $GLOBALS['TYPO3_REQUEST'] = $request;
        $GLOBALS['TSFE']->sys_page = InstanceUtility::get(\TYPO3\CMS\Frontend\Page\PageRepository::class);
        $GLOBALS['TSFE']->getPageAndRootlineWithDomain(1, $request);
        $GLOBALS['TSFE']->fe_user = new FrontendUserAuthentication();
        $GLOBALS['TSFE']->getConfigArray($request);
        $GLOBALS['TSFE']->tmpl->start($GLOBALS['TSFE']->rootLine);

        return $GLOBALS['TSFE'];
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