<?php
declare(strict_types=1);
namespace BR\Toolkit\Typo3\Utility\Link\Processor;

use BR\Toolkit\Exceptions\CacheException;
use BR\Toolkit\Typo3\Utility\Link\Demand\BackendLinkDemandInterface;
use BR\Toolkit\Typo3\Utility\Link\Demand\LinkDemandInterface;
use Exception;
use TYPO3\CMS\Backend\Routing\Exception\RouteNotFoundException;
use TYPO3\CMS\Backend\Routing\UriBuilder;
use TYPO3\CMS\Core\FormProtection\AbstractFormProtection;
use TYPO3\CMS\Core\FormProtection\FormProtectionFactory;
use TYPO3\CMS\Core\Http\Uri;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class BackendLinkProcessor extends AbstractLinkProcessor
{
    private static ?UriBuilder $uriBuilder = null;
    private static ?AbstractFormProtection $backendFormProtection = null;

    public static function supportDemand(LinkDemandInterface $demand): bool
    {
        return ($demand instanceof BackendLinkDemandInterface);
    }

    /**
     * @param LinkDemandInterface|BackendLinkDemandInterface $demand
     * @return string
     * @throws CacheException|RouteNotFoundException
     */
    public function process(LinkDemandInterface|BackendLinkDemandInterface $demand): string
    {
        return $this->generateUri(
            $demand->getModule(),
            $demand->getModuleConfig()
        );
    }

    /**
     * @param string $module
     * @param array $config
     * @return string
     * @throws RouteNotFoundException
     */
    private function generateUri(string $module, array $config): string
    {
        return $this->replaceTokenWithModuleName(
            $this->getUriBuilder()->buildUriFromRoute($module, $config),
            $module
        );
    }

    /**
     * @param Uri $uri
     * @param string $module
     * @return string
     */
    private function replaceTokenWithModuleName(Uri $uri, string $module): string
    {
        $decodeUrl = urldecode(urldecode(urldecode($uri->getQuery())));
        preg_match_all('/token=([a-zA-Z0-9]{40})/m', $decodeUrl, $matches, PREG_SET_ORDER);
        $result = (string)$uri;
        foreach ($matches as $match) {
            $tokenString = $match[0];
            $result = str_replace($tokenString, 'token={{' . $module . '}}', $result);
        }

        return $result;
    }

    public function postProcessLink(string $link): string
    {
        preg_match_all('/token={{(.*?)}}/m', $link, $matches, PREG_SET_ORDER);
        $mutatedLink = $link;
        foreach ($matches as $match) {
            $mutatedLink = str_replace($match[0], 'token=' . $this->generateSecureTokenForRoute($match[1]), $mutatedLink);
        }

        return $mutatedLink;
    }

    /**
     * @param string $routeName
     * @return string
     */
    private function generateSecureTokenForRoute(string $routeName): string
    {
        return $this->getBackendFormProtectionFactory()->generateToken('route', $routeName);
    }

    /**
     * @return AbstractFormProtection
     */
    private function getBackendFormProtectionFactory(): AbstractFormProtection
    {
        return self::$backendFormProtection ?? (self::$backendFormProtection = FormProtectionFactory::get('backend'));
    }

    /**
     * @return UriBuilder
     */
    private function getUriBuilder(): UriBuilder
    {
        return self::$uriBuilder ?? (self::$uriBuilder = GeneralUtility::makeInstance(UriBuilder::class));
    }
}