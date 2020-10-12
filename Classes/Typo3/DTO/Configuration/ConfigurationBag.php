<?php

namespace BR\Toolkit\Typo3\DTO\Configuration;


class ConfigurationBag implements ConfigurationBagInterface
{
    /**
     * @var array
     */
    private $bag;

    /**
     * ConfigurationBag constructor.
     * @param array $config
     */
    public function __construct(array $config)
    {
        $this->bag = $config;
    }

    /**
     * @param string $key
     * @param string $default
     * @return mixed
     */
    public function getValue(string $key, $default = '')
    {
        return $this->bag[$key]??$default;
    }

    /**
     * @param string $path
     * @param string $default
     * @param string $delimiter
     * @return array|mixed|string
     */
    public function getValueFromArrayPath(string $path, $default = '', string $delimiter = '.')
    {
        $pathSegments = explode($delimiter, $path);
        $c = $this->bag;
        foreach ($pathSegments as $segment) {
            if (!is_array($c) || !isset($c[$segment])) {
                return $default;
            }

            $c = $c[$segment];
        }

        return $c;
    }

    /**
     * @param string $key
     * @param string $delimiter
     * @return array
     */
    public function getExplodedIntValue(string $key, string $delimiter = ','): array
    {
        $data = $this->getValue($key, '');
        return $this->intExplode($data, $delimiter);
    }

    /**
     * @param string $path
     * @param string $pathDelimiter
     * @param string $listDelimiter
     * @return array
     */
    public function getExplodedIntValueFromArrayPath(string $path, string $pathDelimiter = '.', string $listDelimiter = ','): array
    {
        $data = $this->getValueFromArrayPath($path,'', $pathDelimiter);
        return $this->intExplode($data, $listDelimiter);
    }

    /**
     * @param string $data
     * @param string $delimiter
     * @return int[]
     */
    private function intExplode(string $data, string $delimiter): array
    {
        $cleanList = [];
        $list = explode($delimiter, $data);
        foreach ($list as $item) {
            if (is_numeric($item)) {
                $cleanList[] = (int)$item;
            }
        }

        return $cleanList;
    }
}
