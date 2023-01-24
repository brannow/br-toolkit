<?php

namespace BR\Toolkit\Misc\Native;

class Curl
{
    /**
     * @param string $url
     * @return false|resource
     */
    public function curlInit(string $url = '')
    {
        return curl_init($url);
    }

    /**
     * @param resource $ch
     * @param int $option
     * @param $value
     * @return bool
     */
    public function curlSetOpt($ch, int $option, $value): bool
    {
        return curl_setopt($ch, $option, $value) ?? false;
    }

    public function curlSetOptArray($ch, array $options): bool
    {
        return curl_setopt_array($ch, $options) ?? false;
    }

    /**
     * @param resource $ch
     * @return bool|string
     */
    public function curlExec($ch)
    {
        return (string)curl_exec($ch);
    }

    /**
     * Get information regarding a specific transfer
     * @link https://php.net/manual/en/function.curl-getinfo.php
     * @param resource $ch
     * @param int $opt [optional] <p>
     * This may be one of the following constants:
     * CURLINFO_EFFECTIVE_URL - Last effective URL
     * @return mixed If opt is given, returns its value as a string.
     * Otherwise, returns an associative array with the following elements
     * (which correspond to opt):
     * "url"
     * "content_type"
     * "http_code"
     * "header_size"
     * "request_size"
     * "filetime"
     * "ssl_verify_result"
     * "redirect_count"
     * "total_time"
     * "namelookup_time"
     * "connect_time"
     * "pretransfer_time"
     * "size_upload"
     * "size_download"
     * "speed_download"
     * "speed_upload"
     * "download_content_length"
     * "upload_content_length"
     * "starttransfer_time"
     * "redirect_time"
     */
    public function curlGetInfo($ch, $opt = null)
    {
        if ($opt === null) {
            return curl_getinfo($ch);
        }

        return curl_getinfo($ch, $opt);
    }

    /**
     * @param resource $ch
     * @return string
     */
    public function curlError($ch): string
    {
        return (string)curl_error($ch);
    }

    /**
     * @param resource $ch
     * @return int
     */
    public function curlErrno($ch): int
    {
        return (int)curl_errno($ch);
    }

    /**
     * @param resource $ch
     */
    public function curlClose($ch)
    {
        curl_close($ch);
    }

    /**
     * @param resource|null $ch
     * @return bool
     */
    public function curlIsOpen($ch): bool
    {
        return (is_resource($ch) && strtolower(get_resource_type($ch) === 'curl'));
    }
}