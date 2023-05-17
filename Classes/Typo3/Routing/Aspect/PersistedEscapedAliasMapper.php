<?php

namespace BR\Toolkit\Typo3\Routing\Aspect;

use Doctrine\DBAL\Connection;
use TYPO3\CMS\Core\Routing\Aspect\PersistedAliasMapper;

class PersistedEscapedAliasMapper extends PersistedAliasMapper
{
    private string $placeholder = '-';
    private bool $deflatePlaceholder = true;
    private bool $uidSuffix = false;
    private bool $lowerCase = false;
    private string $settingsHash;
    private static array $runtimeCache = [];
    private const CHAR_MAPPING = [
            'ä' => 'ae',
            'ö' => 'oe',
            'ü' => 'ue',
            'Ä' => 'Ae',
            'Ö' => 'Oe',
            'Ü' => 'Ue',
            'ß' => 'ss',
        ];

    /**
     * @param array $settings
     * @throws \InvalidArgumentException
     */
    public function __construct(array $settings)
    {
        parent::__construct($settings);
        $this->placeholder = $settings['placeholder'] ?? $this->placeholder;
        $this->deflatePlaceholder = $settings['deflate'] ?? $this->deflatePlaceholder;
        $this->uidSuffix = $settings['uidSuffix'] ?? $this->uidSuffix;
        $this->lowerCase = $settings['lowerCase'] ?? $this->lowerCase;
        $this->settingsHash = md5(serialize($settings));
    }



    /**
     * @param string $value
     * @return string|null
     */
    public function generate(string $value): ?string
    {
        return static::$runtimeCache[$this->settingsHash][$value] ??= $this->replaceNonUrlChars($this->fetchSlugName($value));
    }

    /**
     * @param string $value
     * @return string|null
     */
    private function fetchSlugName(string $value): ?string
    {
        $value = urldecode($value);
        if ($this->tableName === '') {
            $label = urldecode($value);
        } else {
            $this->siteLanguage = $GLOBALS['TSFE']->language;
            $result = $this->findByIdentifier($value);
            $result = $this->resolveOverlay($result);
            if (!isset($result[$this->routeFieldName])) {
                return null;
            }
            $label = (string)$result[$this->routeFieldName];
            if ($this->uidSuffix) {
                $label .= '-' . $result['uid'];
            }
        }

        if ($this->lowerCase) {
            $label = strtolower($label);
        }

        return $this->purgeRouteValuePrefix(
            $label
        );
    }

    /**
     * {@inheritdoc}
     */
    public function resolve(string $value): ?string
    {
        if ($this->uidSuffix) {
            $uidCapture = false;
            $value = $this->extractUidFromSegment($value, $uidCapture);
            if ($uidCapture || $value === null)
                return $value;
        }
        $this->siteLanguage = $GLOBALS['TSFE']->language;
        if ($this->tableName === '') {
            return $value;
        }

        $value = $this->replacePlaceholderWithDBPlaceholder($value);
        return parent::resolve($value);
    }

    /**
     * @param string $value
     * @return string|null
     */
    private function extractUidFromSegment(string $value, bool &$uidCapture = false): ?string
    {
        preg_match_all('/-(\d+)$/m', $value, $matches, PREG_SET_ORDER|PREG_OFFSET_CAPTURE);
        if (!$matches)
            return null;

        $uid = (int)($matches[0][1][0]??0);
        if ($uid > 0) {
            $uidCapture = true;
            return (string)$uid;
        }


        $pos = $matches[0][0][1]??0;
        return str_split($value, $pos)[0]??$value;
    }

    /**
     * @param string|null $value
     * @return string
     */
    private function replaceNonUrlChars(?string $value): ?string
    {
        if (!$value)
            return null;

        $value = str_replace(array_keys(self::CHAR_MAPPING), self::CHAR_MAPPING, $value);
        $value = preg_replace('/[^a-zA-Z0-9]/m', $this->placeholder, $value);

        if ($this->deflatePlaceholder) {
            $value = str_replace(['------', '-----', '----', '---', '--'], '-', $value);
        }
        return $value;
    }

    /**
     * @param string $value
     * @return string
     */
    private function replacePlaceholderWithDBPlaceholder(string $value): string
    {
        $dbPlaceholder = '_';
        if ($this->deflatePlaceholder) {
            $dbPlaceholder = '%';
        }

        $mapping = static::CHAR_MAPPING;
        $mapping[$dbPlaceholder] = $this->placeholder;
        return str_replace($mapping, array_keys($mapping), $value);
    }

    /**
     * @param string $value
     * @return array|null
     */
    protected function findByRouteFieldValue(string $value): ?array
    {
        $languageAware = $this->languageFieldName !== null && $this->languageParentFieldName !== null;

        $queryBuilder = $this->createQueryBuilder();
        $constraints = [
            $queryBuilder->expr()->like(
                $this->routeFieldName,
                $queryBuilder->createNamedParameter($value, \PDO::PARAM_STR)
            ),
        ];

        $languageIds = null;
        if ($languageAware) {
            $languageIds = $this->resolveAllRelevantLanguageIds();
            $constraints[] = $queryBuilder->expr()->in(
                $this->languageFieldName,
                $queryBuilder->createNamedParameter($languageIds, Connection::PARAM_INT_ARRAY)
            );
        }

        $results = $queryBuilder
            ->select(...$this->persistenceFieldNames)
            ->where(...$constraints)
            ->executeQuery()
            ->fetchAllAssociative();
        // limit results to be contained in rootPageId of current Site
        // (which is defining the route configuration currently being processed)
        if ($this->slugUniqueInSite) {
            $results = array_values($this->filterContainedInSite($results));
        }
        // return first result record in case table is not language aware
        if (!$languageAware) {
            return $results[0] ?? null;
        }
        // post-process language fallbacks
        return $this->resolveLanguageFallback($results, $this->languageFieldName, $languageIds);
    }
}
