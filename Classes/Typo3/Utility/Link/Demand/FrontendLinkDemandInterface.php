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

    public function setTargetPageType(int $pageType): static;
    public function setNoCache(bool $noCache): static;
    public function setSection(string $section): static;
    public function setFormat(string $format): static;
    public function setLinkAccessRestrictedPages(bool $access): static;
    public function setArguments(array $arguments): static;
    public function setCreateAbsoluteUri(bool $absolute): static;
    public function setAddQueryString(bool $queryString): static;
    public function setArgumentsToBeExcludedFromQueryString(array $argumentsToBeExcludedFromQueryString): static;
    public function setArgumentPrefix(string $argumentPrefix): static;
    public function setTargetPageUid(?int $pageUid): static;
}