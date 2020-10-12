<?php

namespace BR\Toolkit\Misc\DTO\Curl;

interface CurlResponseInterface
{
    /**
     * @return bool
     */
    public function isError(): bool;

    /**
     * @return string
     */
    public function getError(): string;

    /**
     * @return array
     */
    public function getArrayDataFromJson(): array;
}