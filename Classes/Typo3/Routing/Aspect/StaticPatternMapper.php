<?php

namespace BR\Toolkit\Typo3\Routing\Aspect;

use TYPO3\CMS\Core\Routing\Aspect\StaticMappableAspectInterface;

class StaticPatternMapper implements StaticMappableAspectInterface
{
    private string $pattern;

    /**
     * @param array $settings
     * @throws \InvalidArgumentException
     */
    public function __construct(array $settings)
    {
        $this->pattern = $settings['pattern']??'';
    }

    /**
     * @param string $value
     * @return string|null
     */
    public function generate(string $value): ?string
    {
        if ($this->pattern !== '' && preg_match('/'.$this->pattern.'/', $value)) {
            return $value;
        }

        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function resolve(string $value): ?string
    {
        if ($this->pattern !== '' && preg_match('/'.$this->pattern.'/', $value)) {
            return $value;
        }

        return null;
    }
}
