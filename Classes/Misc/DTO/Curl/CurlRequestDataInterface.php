<?php

namespace BR\Toolkit\Misc\DTO\Curl;

interface CurlRequestDataInterface
{
    /**
     * @return string
     */
    public function getUrl(): string;

    /**
     * @param string $url
     * @return CurlRequestDataInterface
     */
    public function setUrl(string $url): CurlRequestDataInterface;

    /**
     * @param string $method
     * @return CurlRequestDataInterface
     */
    public function setMethod(string $method): CurlRequestDataInterface;

    /**
     * @return string
     */
    public function getMethod(): string;

    /**
     * @return bool
     */
    public function isPost(): bool;

    /**
     * @param string $key
     * @param string $value
     * @return CurlRequestDataInterface
     */
    public function setQuery(string $key, string $value): CurlRequestDataInterface;

    /**
     * @return string[]
     */
    public function getQuery(): array;

    /**
     * @return string
     */
    public function getQueryString(): string;

    /**
     * @param string $key
     * @param string $value
     * @return CurlRequestDataInterface
     */
    public function setData(string $key, string $value): CurlRequestDataInterface;

    /**
     * @return string[]
     */
    public function getData(): array;

    /**
     * @return string
     */
    public function getDataString(): string;
}