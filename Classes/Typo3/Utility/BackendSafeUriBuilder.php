<?php

namespace BR\Toolkit\Typo3\Utility;

use TYPO3\CMS\Core\Site\Entity\Site;
use TYPO3\CMS\Core\Site\Entity\SiteLanguage;
use TYPO3\CMS\Extbase\Mvc\Web\Routing\UriBuilder;

/**
 * Backend-safe UriBuilder that uses native TYPO3 Site routing
 *
 * This builder can be used as a drop-in replacement for the standard UriBuilder
 * in backend context, preventing session corruption issues.
 */
class BackendSafeUriBuilder extends UriBuilder
{
    /**
     * @var int|null
     */
    protected $targetPageUid;

    /**
     * @var array
     */
    protected $arguments = [];

    /**
     * @var bool
     */
    protected $createAbsoluteUri = false;

    /**
     * @var int
     */
    protected $targetPageType = 0;

    /**
     * @var int
     */
    protected $languageId = 0;

    /**
     * @var Site|null
     */
    protected $site;

    /**
     * @var SiteLanguage|null
     */
    protected $siteLanguage;

    /**
     * @param Site|null $site
     * @param SiteLanguage|null $siteLanguage
     */
    public function __construct(?Site $site = null, ?SiteLanguage $siteLanguage = null)
    {
        $this->site = $site;
        $this->siteLanguage = $siteLanguage;
        if ($siteLanguage) {
            $this->languageId = $siteLanguage->getLanguageId();
        }
    }

    /**
     * @param int $targetPageUid
     * @return $this
     */
    public function setTargetPageUid($targetPageUid): self
    {
        $this->targetPageUid = (int)$targetPageUid;
        return $this;
    }

    /**
     * @param int $targetPageType
     * @return $this
     */
    public function setTargetPageType($targetPageType): self
    {
        $this->targetPageType = (int)$targetPageType;
        return $this;
    }

    /**
     * @param array $arguments
     * @return $this
     */
    public function setArguments(array $arguments): self
    {
        $this->arguments = $arguments;
        return $this;
    }

    /**
     * @param bool $createAbsoluteUri
     * @return $this
     */
    public function setCreateAbsoluteUri($createAbsoluteUri): self
    {
        $this->createAbsoluteUri = (bool)$createAbsoluteUri;
        return $this;
    }

    /**
     * @return $this
     */
    public function reset(): self
    {
        $this->targetPageUid = null;
        $this->arguments = [];
        $this->createAbsoluteUri = false;
        $this->targetPageType = 0;
        return $this;
    }

    /**
     * Build the URI using backend-safe Site routing
     *
     * @return string
     */
    public function build(): string
    {
        if ($this->targetPageUid === null) {
            return '';
        }

        // Add page type to arguments if set
        $arguments = $this->arguments;
        if ($this->targetPageType > 0) {
            $arguments['type'] = $this->targetPageType;
        }

        return FrontendUtility::generateBackendSafeLink(
            $this->targetPageUid,
            $arguments,
            $this->languageId
        );
    }

    /**
     * Builds the URI but doesn't apply TYPO3 cHash
     * In our case, this is the same as build() since Site routing handles cHash
     *
     * @return string
     */
    public function buildFrontendUri(): string
    {
        return $this->build();
    }
}
