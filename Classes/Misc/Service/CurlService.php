<?php

namespace BR\Toolkit\Misc\Service;

use BR\Toolkit\Misc\DTO\Curl\CurlRequest;
use BR\Toolkit\Misc\DTO\Curl\CurlRequestInterface;
use BR\Toolkit\Misc\DTO\Curl\CurlRequestOptionsInterface;
use BR\Toolkit\Misc\DTO\Curl\CurlResponse;
use BR\Toolkit\Misc\DTO\Curl\CurlResponseInterface;
use BR\Toolkit\Misc\Native\Curl;

class CurlService
{
    /**
     * @var Curl
     */
    private $curlAdapter;

    /**
     * @var resource|null
     */
    private $connection;

    /**
     * CurlService constructor.
     * @param Curl $curlAdapter
     */
    public function __construct(Curl $curlAdapter)
    {
        $this->curlAdapter = $curlAdapter;
    }

    /**
     * @param string $url
     * @param string $method
     * @return CurlRequestInterface
     */
    protected function getCurlRequest(string $url = '', string $method = 'GET'): CurlRequestInterface
    {
        $request = new CurlRequest();
        $request->setUrl($url);
        $request->setMethod($method);
        $request
            ->setOption(CURLOPT_RETURNTRANSFER, true)
            ->setOption(CURLOPT_MAXREDIRS, 5)
            ->setOption(CURLOPT_FOLLOWLOCATION, 5)
            ->setOption(CURLOPT_TIMEOUT, 2000)
            ->setOption(CURLOPT_CONNECTTIMEOUT, 2000)
            ->setOption(CURLOPT_SSL_VERIFYPEER, false);
        return $request;
    }

    /**
     * @param CurlRequestInterface $curlRequest
     * @return CurlResponseInterface
     */
    protected function execute(CurlRequestInterface $curlRequest): CurlResponseInterface
    {
        $this->setupConnection($curlRequest);
        return $this->sendRequest($curlRequest);
    }

    /**
     * @param CurlRequestInterface $request
     * @return CurlResponseInterface
     */
    private function sendRequest(CurlRequestInterface $request): CurlResponseInterface
    {
        $this->curlAdapter->curlSetOpt($this->connection, CURLOPT_URL, $request->getUrl());
        $this->curlAdapter->curlSetOpt($this->connection, CURLOPT_POST, $request->isPost());
        $this->curlAdapter->curlSetOpt($this->connection, CURLOPT_POSTFIELDS, $request->getDataString());
        $this->curlAdapter->curlSetOpt($this->connection, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Content-Length: ' . strlen($request->getDataString())
        ]);

        $data = $this->curlAdapter->curlExec($this->connection);
        $header = $this->curlAdapter->curlGetInfo($this->connection);
        $error = $this->curlAdapter->curlError($this->connection);
        $errno = $this->curlAdapter->curlErrno($this->connection);

        return new CurlResponse($data, $header, $error, $errno);
    }

    /**
     * @param CurlRequestOptionsInterface $curlRequestOptions
     */
    private function setupConnection(CurlRequestOptionsInterface $curlRequestOptions): void
    {
        if (!$this->curlAdapter->curlIsOpen($this->connection)) {
            $this->connection = $this->curlAdapter->curlInit();
        }
        $this->curlAdapter->curlSetOptArray($this->connection, $curlRequestOptions->getOptions());
    }

    /**
     *
     */
    protected function closeConnection(): void
    {
        if ($this->curlAdapter->curlIsOpen($this->connection)) {
            $this->curlAdapter->curlClose($this->connection);
            $this->connection = null;
        }
    }
}