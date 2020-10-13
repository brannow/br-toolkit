<?php

namespace BR\Toolkit\Misc\DTO\Curl;

class CurlRequest implements CurlRequestInterface
{
    /**
     * @var CurlRequestOptionsInterface
     */
    private $options;

    /**
     * @var CurlRequestDataInterface
     */
    private $data;

    /**
     * CurlRequest constructor.
     */
    public function __construct()
    {
        $this->options = new CurlRequestOptions();
        $this->data = new CurlRequestData();
    }

    /**
     * @return string
     */
    public function getQueryString(): string
    {
        return $this->data->getQueryString();
    }

    /**
     * @return string
     */
    public function getDataString(): string
    {
        return $this->data->getDataString();
    }

    /**
     * @param int $key
     * @param $value
     * @return CurlRequestOptionsInterface
     */
    public function setOption(int $key, $value): CurlRequestOptionsInterface
    {
        $this->options->setOption($key, $value);
        return $this;
    }

    /**
     * @return array
     */
    public function getOptions(): array
    {
        return $this->options->getOptions();
    }

    /**
     * @return string
     */
    public function getUrl(): string
    {
        return $this->data->getUrl();
    }

    /**
     * @param string $url
     * @return CurlRequestDataInterface
     */
    public function setUrl(string $url): CurlRequestDataInterface
    {
        $this->data->setUrl($url);
        return $this;
    }

    /**
     * @param string $method
     * @return CurlRequestDataInterface
     */
    public function setMethod(string $method): CurlRequestDataInterface
    {
        $this->data->setMethod($method);
        return $this;
    }

    /**
     * @return string
     */
    public function getMethod(): string
    {
        return $this->data->getMethod();
    }

    /**
     * @return bool
     */
    public function isPost(): bool
    {
        return $this->data->isPost();
    }

    /**
     * @param string $key
     * @param string $value
     * @return CurlRequestDataInterface
     */
    public function setQuery(string $key, string $value): CurlRequestDataInterface
    {
        $this->data->setQuery($key, $value);
        return $this;
    }

    /**
     * @return array
     */
    public function getQuery(): array
    {
        return $this->data->getQuery();
    }

    /**
     * @param string $key
     * @param string $value
     * @return CurlRequestDataInterface
     */
    public function setData(string $key, string $value): CurlRequestDataInterface
    {
        $this->data->setData($key, $value);
        return $this;
    }

    /**
     * @return array
     */
    public function getData(): array
    {
        return $this->data->getData();
    }
}