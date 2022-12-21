<?php
declare(strict_types=1);
namespace BR\Toolkit\Typo3\Utility\Link\Demand;

use BR\Toolkit\Typo3\Utility\FrontendUtility;

/**
 * $demand = new FrontendLinkDemand();
 * $demand->setTargetPageUid(1);
 * $link = LinkUtility::getLink($demand);
 */
class FrontendLinkDemand extends AbstractLinkDemand implements FrontendLinkDemandInterface
{
    protected int $languageId = -1;

    private ?int $pageUid = null;
    private int $pageType = 0;
    private bool $noCache = false;
    private string $section = '';
    private string $format = '';
    private bool $linkAccessRestrictedPages = false;
    private array $arguments = [];
    private bool $absoluteUri = false;
    private bool $queryStringsAllow = false;
    private array $argumentsToBeExcludedFromQueryString = [];
    private string $argumentPrefix = '';

    public function getContext(): string
    {
        return 'frontend';
    }

    protected function getCacheIdentifier(): array
    {
        return [
            $this->getTargetPageUid(),
            $this->getTargetPageType(),
            $this->getNoCache(),
            $this->getSection(),
            $this->getFormat(),
            $this->getLinkAccessRestrictedPages(),
            $this->getArguments(),
            $this->getCreateAbsoluteUri(),
            $this->getAddQueryString(),
            $this->getArgumentsToBeExcludedFromQueryString(),
            $this->getArgumentPrefix(),
            $this->getLanguageId()
        ];
    }

    public function getBuilderMapping(): array
    {
        // UriBuilder::method_name => value
        return array_filter([
            'setTargetPageType' => $this->getTargetPageType(),
            'setNoCache' => $this->getNoCache(),
            'setSection' => $this->getSection(),
            'setLanguage' => (string)$this->getLanguageId(), // typo3 fuck-up
            'setLinkAccessRestrictedPages' => $this->getLinkAccessRestrictedPages(),
            'setArguments' => $this->getArguments(),
            'setCreateAbsoluteUri' => $this->getCreateAbsoluteUri(),
            'setAddQueryString' => $this->getAddQueryString(),
            'setArgumentsToBeExcludedFromQueryString' => $this->getArgumentsToBeExcludedFromQueryString(),
            'setTargetPageUid' => $this->getTargetPageUid(),
        ]);
    }

    public function getTargetPageType(): int
    {
        return $this->pageType;
    }

    public function getNoCache(): bool
    {
        return $this->noCache;
    }

    public function getSection(): string
    {
        return $this->section;
    }

    public function getFormat(): string
    {
        return $this->format;
    }

    public function getLinkAccessRestrictedPages(): bool
    {
        return $this->linkAccessRestrictedPages;
    }

    public function getArguments(): array
    {
        return $this->arguments;
    }

    public function getCreateAbsoluteUri(): bool
    {
        return $this->absoluteUri;
    }

    public function getAddQueryString(): bool
    {
        return $this->queryStringsAllow;
    }

    public function getArgumentsToBeExcludedFromQueryString(): array
    {
        return $this->argumentsToBeExcludedFromQueryString;
    }

    public function getArgumentPrefix(): string
    {
        return $this->argumentPrefix;
    }

    public function getTargetPageUid(): ?int
    {
        return $this->pageUid;
    }

    public function setTargetPageType(int $pageType): static
    {
        $this->pageType = $pageType;
        return $this;
    }

    public function setNoCache(bool $noCache): static
    {
        $this->noCache = $noCache;
        return $this;
    }

    public function setSection(string $section): static
    {
        $this->section = $section;
        return $this;
    }

    public function setFormat(string $format): static
    {
        $this->format = $format;
        return $this;
    }

    public function setLinkAccessRestrictedPages(bool $access): static
    {
        $this->linkAccessRestrictedPages = $access;
        return $this;
    }

    public function setArguments(array $arguments): static
    {
        $this->arguments = $arguments;
        return $this;
    }

    public function setCreateAbsoluteUri(bool $absolute): static
    {
        $this->absoluteUri = $absolute;
        return $this;
    }

    public function setAddQueryString(bool $queryString): static
    {
        $this->queryStringsAllow = $queryString;
        return $this;
    }

    public function setArgumentsToBeExcludedFromQueryString(array $argumentsToBeExcludedFromQueryString): static
    {
        $this->argumentsToBeExcludedFromQueryString =  $argumentsToBeExcludedFromQueryString;
        return $this;
    }

    public function setArgumentPrefix(string $argumentPrefix): static
    {
        $this->argumentPrefix = $argumentPrefix;
        return $this;
    }

    public function setTargetPageUid(?int $pageUid): static
    {
        $this->pageUid = $pageUid;
        return $this;
    }

    public function getLanguageId(): int
    {
        if ($this->languageId === -1) {
            try {
                $tsfc = FrontendUtility::getFrontendController();
                $this->setLanguageId($tsfc->getLanguage()->getLanguageId());
            } catch (\Exception $_) {
                $this->setLanguageId(0);
            }
        }

        return $this->languageId;
    }
}