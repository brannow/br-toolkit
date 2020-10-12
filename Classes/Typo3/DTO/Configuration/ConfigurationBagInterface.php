<?php

namespace BR\Toolkit\Typo3\DTO\Configuration;

interface ConfigurationBagInterface
{
    /**
     * @param string $key
     * @param string $default
     * @return mixed
     */
    public function getValue(string $key, $default = '');

    /**
     * @param string $path
     * @param string $default
     * @param string $delimiter
     * @return array|mixed|string
     */
    public function getValueFromArrayPath(string $path, $default = '', string $delimiter = '.');

    /**
     * @param string $key
     * @param string $delimiter
     * @return array
     */
    public function getExplodedIntValue(string $key, string $delimiter = ','): array;

    /**
     * @param string $path
     * @param string $pathDelimiter
     * @param string $listDelimiter
     * @return array
     */
    public function getExplodedIntValueFromArrayPath(string $path, string $pathDelimiter = '.', string $listDelimiter = ','): array;
}
