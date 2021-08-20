<?php

namespace BR\Toolkit\Tests\Typo3\Utility;


use BR\Toolkit\Tests\Unit\Typo3\MockDiContainer;
use BR\Toolkit\Typo3\Utility\FrontendUtility;
use BR\Toolkit\Typo3\VersionWrapper\InstanceUtility;
use Doctrine\DBAL\Driver\Statement;
use Doctrine\DBAL\ForwardCompatibility\DriverStatement;
use PHPUnit\Framework\TestCase;
use TYPO3\CMS\Backend\FrontendBackendUserAuthentication;
use TYPO3\CMS\Core\Cache\CacheManager;
use TYPO3\CMS\Core\Cache\Frontend\VariableFrontend;
use TYPO3\CMS\Core\Core\Environment;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Database\Query\QueryBuilder;
use TYPO3\CMS\Core\Database\Query\Restriction\QueryRestrictionContainerInterface;
use TYPO3\CMS\Core\Localization\LanguageServiceFactory;
use TYPO3\CMS\Core\Package\PackageManager;
use TYPO3\CMS\Core\Site\Entity\Site;
use TYPO3\CMS\Core\Site\SiteFinder;
use TYPO3\CMS\Frontend\Authentication\FrontendUserAuthentication;
use TYPO3\CMS\Frontend\Configuration\TypoScript\ConditionMatching\ConditionMatcher;
use TYPO3\CMS\Frontend\Middleware\TypoScriptFrontendInitialization;
use TYPO3\CMS\Core\Context\Context;

class FrontendUtilityTest extends TestCase
{
    /** @var MockDiContainer */
    private $container = null;

    public function setUp(): void
    {
        $this->container = MockDiContainer::injectGeneralUtility();
        $siteConfig = $this->getMockBuilder(SiteFinder::class)->disableOriginalConstructor()->getMock();
        $siteConfig->expects($this->any())
            ->method('getSiteByPageId')
            ->willReturn(new Site('test', 1, []));
        $this->container->set(SiteFinder::class, $siteConfig);

        $frontendUserAuth = $this->getMockBuilder(FrontendUserAuthentication::class)->disableOriginalConstructor()->getMock();
        $this->container->set(FrontendUserAuthentication::class, $frontendUserAuth);

        $init = new TypoScriptFrontendInitialization(InstanceUtility::get(Context::class));
        $this->container->set(TypoScriptFrontendInitialization::class, $init);


        $languageServiceFactory = $this->getMockBuilder(LanguageServiceFactory::class)->disableOriginalConstructor()->getMock();
        $this->container->set(LanguageServiceFactory::class, $languageServiceFactory);

        $pm = $this->getMockBuilder(PackageManager::class)->disableOriginalConstructor()->getMock();
        $this->container->set(PackageManager::class, $pm);
        $pm->expects($this->any())
            ->method('getActivePackages')
            ->willReturn([]);

        $this->container->set(ConditionMatcher::class, $this->getMockBuilder(ConditionMatcher::class)->disableOriginalConstructor()->getMock());

        $GLOBALS['BE_USER'] = new FrontendBackendUserAuthentication();

        $qbMock = $this->getMockBuilder(QueryBuilder::class)->disableOriginalConstructor()->getMock();
        $pool = $this->getMockBuilder(ConnectionPool::class)->disableOriginalConstructor()->getMock();
        $this->container->set(ConnectionPool::class, $pool);
        $this->container->set(ConnectionPool::class, $pool);
        $this->container->set(ConnectionPool::class, $pool);
        $this->container->set(ConnectionPool::class, $pool);
        $this->container->set(ConnectionPool::class, $pool);
        $this->container->set(ConnectionPool::class, $pool);
        $this->container->set(ConnectionPool::class, $pool);


        $pool->expects($this->any())
            ->method('getQueryBuilderForTable')
            ->willReturn($qbMock);

        $statement = $this->getMockBuilder(Statement::class)->getMock();

        $qbMock->expects($this->any())
            ->method('execute')
            ->willReturn($statement);
        $qbMock->expects($this->any())
            ->method('select')
            ->willReturn($qbMock);
        $qbMock->expects($this->any())
            ->method('from')
            ->willReturn($qbMock);
        $qbMock->expects($this->any())
            ->method('where')
            ->willReturn($qbMock);
        $qbMock->expects($this->any())
            ->method('orderBy')
            ->willReturn($qbMock);
        $qbMock->expects($this->any())
            ->method('addOrderBy')
            ->willReturn($qbMock);
        $qbMock->expects($this->any())
            ->method('setMaxResults')
            ->willReturn($qbMock);

        $resBuilder = $this->getMockBuilder(QueryRestrictionContainerInterface::class)->getMock();
        $resBuilder->expects($this->any())
            ->method($this->anything())
            ->willReturn($resBuilder);

        $qbMock->expects($this->any())
            ->method('getRestrictions')
            ->willReturn($resBuilder);

        $statement->expects($this->any())
            ->method('fetch')
            ->willReturn([
                'uid' => 1,
                'pid' => 0,
                'hidden' => 0,
                'starttime' => 0,
                'endtime' => 0,
                'sys_language_uid' => 0,
                'doktype' => 0,
                'fe_login_mode' => 0,
                'extendToSubpages' => false,
                'root' => 1,
                'title' => '',
                'config' => '',
                'sitetitle' => '',
                'constants' => '',
                'tsconfig_includes' => '',
                'TSconfig' => 'TSFE.constants.0 = 0'
            ]);

        $GLOBALS['TCA']['pages']['ctrl']['languageField'] = 'sys_language_uid';
        $GLOBALS['TCA']['pages']['ctrl']['delete'] = 'delete';
        $GLOBALS['TCA']['pages']['ctrl']['enablecolumns'] = '';
        $GLOBALS['TCA']['pages']['columns'] = [];

    }

    public function testFrontendUtilityCreation()
    {
        $oldReporting = error_reporting();
        error_reporting(E_ALL & ~E_NOTICE & ~E_STRICT & ~E_DEPRECATED);

        $cache = $this->getMockBuilder(VariableFrontend::class)->disableOriginalConstructor()->getMock();
        $cacheManager = $this->getMockBuilder(CacheManager::class)->disableOriginalConstructor()->getMock();
        $this->container->set(CacheManager::class, $cacheManager);
        $cacheManager->expects($this->any())
            ->method('getCache')
            ->willReturn($cache);
            $cache->expects($this->any())
                ->method('get')
                ->willReturnOnConsecutiveCalls(
                    '1=1','1=1',
                    [
                        'uid' => 1,
                        'pid' => 0,
                        'hidden' => 0,
                        'starttime' => 0,
                        'endtime' => 0,
                        'sys_language_uid' => 0,
                        'doktype' => 0,
                        'tstamp' => 0,
                        'SYS_LASTCHANGED' => 0,
                        'extendToSubpages' => false
                    ],
                    '1=1','1=1',[], ['data'], [
                    'constants' => [],
                    'setup' => [
                        'types.' => [
                            0 => 'test'
                        ],
                        'config.' => [
                            'no_cache' => true
                        ],
                        'test.' => []
                    ]
                ],
                    '1=1','1=1','1=1','1=1','1=1','1=1'
                );

        $tsfe = FrontendUtility::getFrontendController();
        $this->assertEquals(1, $tsfe->id);

        error_reporting($oldReporting);
    }
}