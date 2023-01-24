<?php
declare(strict_types=1);
namespace BR\Toolkit\Typo3\Utility\Link\Demand;

interface FrontendLinkDemandInterface extends LinkDemandInterface
{
    public function getBuilderMapping(): array;

    public function getTargetPageType(): int;
    public function getNoCache(): bool;
    public function getSection(): string;
    public function getFormat(): string;
    public function getLinkAccessRestrictedPages(): bool;
    public function getArguments(): array;
    public function getCreateAbsoluteUri(): bool;
    public function getAddQueryString(): bool;
    public function getArgumentsToBeExcludedFromQueryString(): array;
    public function getArgumentPrefix(): string;
    public function getTargetPageUid(): ?int;

    public function setTargetPageType(int $pageType): FrontendLinkDemandInterface;
    public function setNoCache(bool $noCache): FrontendLinkDemandInterface;
    public function setSection(string $section): FrontendLinkDemandInterface;
    public function setFormat(string $format): FrontendLinkDemandInterface;
    public function setLinkAccessRestrictedPages(bool $access): FrontendLinkDemandInterface;
    public function setArguments(array $arguments): FrontendLinkDemandInterface;
    public function setCreateAbsoluteUri(bool $absolute): FrontendLinkDemandInterface;
    public function setAddQueryString(bool $queryString): FrontendLinkDemandInterface;
    public function setArgumentsToBeExcludedFromQueryString(array $argumentsToBeExcludedFromQueryString): FrontendLinkDemandInterface;
    public function setArgumentPrefix(string $argumentPrefix): FrontendLinkDemandInterface;
    public function setTargetPageUid(?int $pageUid): FrontendLinkDemandInterface;
}