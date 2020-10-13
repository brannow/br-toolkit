<?php


namespace BR\Toolkit\Misc\DTO\Curl;


class CurlRequestData implements CurlRequestDataInterface
{
    private $url = '';
    private $method = 'GET';
    private $query = [];
    private $data = [];

    /**
     * @return string
     */
    public function getUrl(): string
    {
        if (!empty($this->getQuery())) {
            $queryString = $this->getQueryString();
            // check if url already had query information
            if (parse_url($this->url, PHP_URL_QUERY)) {
                return $this->url . '&' . $queryString;
            } else {
                return $this->url . '?' . $queryString;
            }
        }

        return $this->url;
    }

    /**
     * @param string $url
     * @return CurlRequestDataInterface
     */
    public function setUrl(string $url): CurlRequestDataInterface
    {
        $this->url = $url;
        return $this;
    }

    /**
     * @param string $method
     * @return CurlRequestDataInterface
     */
    public function setMethod(string $method): CurlRequestDataInterface
    {
        $this->method = $method;
        return $this;
    }

    /**
     * @return string
     */
    public function getMethod(): string
    {
        return $this->method;
    }

    /**
     * @return bool
     */
    public function isPost(): bool
    {
        return (strtoupper($this->getMethod()) === 'POST');
    }

    /**
     * @param string $key
     * @param string $value
     * @return CurlRequestDataInterface
     */
    public function setQuery(string $key, string $value): CurlRequestDataInterface
    {
        $this->query[$key] = $value;
        return $this;
    }

    /**
     * @return array
     */
    public function getQuery(): array
    {
        return $this->query;
    }

    /**
     * @return string
     */
    public function getQueryString(): string
    {
        return http_build_query($this->getQuery());
    }

    /**
     * @param string $key
     * @param string $value
     * @return CurlRequestDataInterface
     */
    public function setData(string $key, string $value): CurlRequestDataInterface
    {
        $this->data[$key] = $value;
        return $this;
    }

    /**
     * @return array
     */
    public function getData(): array
    {
        return $this->data;
    }

    /**
     * @return string
     */
    public function getDataString(): string
    {
        return http_build_query($this->getData());
    }
}