<?php
declare(strict_types=1);
namespace BR\Toolkit\Typo3\Utility\Link\Demand;

interface LinkDemandInterface
{
    public const CACHE_LEVEL_DISABLE = -1;
    public const CACHE_LEVEL_RUNTIME = 1;
    public const CACHE_LEVEL_PERSIST = 0;

    public function getCacheLevel(): int;
    public function getCacheKey(): string;
    public function getCacheLifeTime(): int;
    public function getLanguageId(): int;
    public function setLanguageId(int $languageId): static;

    public function getContext(): string;
}