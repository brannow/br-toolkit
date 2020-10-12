<?php

namespace BR\Toolkit\Misc\DTO\Curl;

interface CurlRequestOptionsInterface
{
    /**
     * @return array
     */
    public function getOptions(): array;

    /**
     * @param int $key
     * @param $value
     * @return CurlRequestOptionsInterface
     */
    public function setOption(int $key, $value): CurlRequestOptionsInterface;
}