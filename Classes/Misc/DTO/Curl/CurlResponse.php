<?php

namespace BR\Toolkit\Misc\DTO\Curl;

class CurlResponse implements CurlResponseInterface
{
    /**
     * @var string
     */
    private $data;
    /**
     * @var array
     */
    private $headers;
    /**
     * @var string
     */
    private $error;
    /**
     * @var int
     */
    private $errno;

    /**#
     * CurlResponse constructor.
     * @param string $data
     * @param array $headers
     * @param string $error
     * @param int $errno
     */
    public function __construct(string $data, array $headers, string $error, int $errno)
    {
        $this->data = $data;
        $this->headers = $headers;
        $this->error = $error;
        $this->errno = $errno;
    }

    /**
     * @return bool
     */
    public function isError(): bool
    {
        return $this->errno !== 0;
    }

    /**
     * @return string
     */
    public function getError(): string
    {
        return $this->error;
    }

    /**
     * @return array
     */
    public function getArrayDataFromJson(): array
    {
        return json_decode($this->data, true) ?? [];
    }
}