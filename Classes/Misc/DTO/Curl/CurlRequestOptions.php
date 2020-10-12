<?php


namespace BR\Toolkit\Misc\DTO\Curl;


class CurlRequestOptions implements CurlRequestOptionsInterface
{
    /**
     * @var array
     */
    private $options = [];

    /**
     * @return array
     */
    public function getOptions(): array
    {
        return $this->options;
    }

    /**
     * @param int $key
     * @param mixed $value
     * @return CurlRequestOptionsInterface
     */
    public function setOption(int $key, $value): CurlRequestOptionsInterface
    {
        $this->options[$key] = $value;
        return $this;
    }
}