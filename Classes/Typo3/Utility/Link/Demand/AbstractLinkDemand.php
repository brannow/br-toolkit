<?php
declare(strict_types=1);
namespace BR\Toolkit\Typo3\Utility\Link\Demand;

abstract class AbstractLinkDemand implements LinkDemandInterface
{
    protected const CACHE_LIFETIME_DEFAULT = 3600;
    protected int $languageId = 0;
    private int $cacheLevel = LinkDemandInterface::CACHE_LEVEL_PERSIST;

    public function getCacheLevel(): int
    {
        return $this->cacheLevel;
    }

    public function setCacheLevel(int $cacheLevel): static
    {
        $this->cacheLevel = $cacheLevel;
        return $this;
    }

    abstract protected function getCacheIdentifier(): array;

    public function getCacheKey(): string
    {
        return md5(serialize($this->getCacheIdentifier()));
    }

    public function getCacheLifeTime(): int
    {
        return static::CACHE_LIFETIME_DEFAULT;
    }

    public function getLanguageId(): int
    {
        return $this->languageId;
    }

    public function setLanguageId(int $languageId): static
    {
        $this->languageId = $languageId;
        return $this;
    }

}