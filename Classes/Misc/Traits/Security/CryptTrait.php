<?php


namespace BR\Toolkit\Misc\Traits\Security;

trait CryptTrait
{
    private static $CYPHER = 'aes-128-gcm';
    private static $TAG_LEN = 4;

    private static $SEPARATOR = ':';

    /**
     * base64 to base64 url convert map
     * @var array
     */
    private static $charRemap = [
        '/' => '_',
        '+' => '-'
    ];

    /**
     * @param string $content
     * @param string $secret
     * @param string $salt
     * @return string
     */
    protected static function encrypt(string $content, string $secret, string $salt = ''): string
    {
        $iv = self::getInitializationVector();
        $cipherText = openssl_encrypt($content, self::$CYPHER, $secret, $options=0, $iv, $tag, $salt, self::$TAG_LEN);
        $segments = self::encodeSpecialMapBatch($cipherText, self::maskTagAndVector($tag, $iv));
        return implode(self::$SEPARATOR, $segments);
    }

    /**
     * @param string $data
     * @param string $secret
     * @param string $salt
     * @return string|null
     */
    protected static function decrypt(string $data, string $secret, string $salt = ''): ?string
    {
        $dataMask = explode(self::$SEPARATOR, $data);
        $dataMask = self::decodeSpecialMapBatch(...$dataMask);
        if (count($dataMask) === 2) {
            $unmaskData = self::unmaskTagAndVector($dataMask[1]);
            $output = openssl_decrypt($dataMask[0], self::$CYPHER, $secret, $options=0, $unmaskData['iv'], $unmaskData['tag'], $salt);
            return $output === false ? null : $output;
        }

        return null;
    }

    /**
     * @param string ...$segments
     * @return array
     */
    private static function encodeSpecialMapBatch(string ...$segments): array
    {
        $c = count($segments);
        for ($i = 0; $i < $c; $i++) {
            $segments[$i] = self::encodeSpecialMap($segments[$i]);
        }

        return $segments;
    }

    /**
     * @param string ...$segments
     * @return array
     */
    private static function decodeSpecialMapBatch(string ...$segments): array
    {
        $c = count($segments);
        for ($i = 0; $i < $c; $i++) {
            $segments[$i] = self::decodeSpecialMap($segments[$i]);
        }

        return $segments;
    }

    /**
     * @internal
     * @param string $baseString
     * @return string
     */
    private static function encodeSpecialMap(string $baseString): string
    {
        $encodeString = str_replace(array_keys(self::$charRemap), self::$charRemap, $baseString);
        // remove possible equals marks at the end of the base64 string, replace with a static offset number (0-2)
        if (substr($encodeString, -1) === '=') {
            $encodeString = str_replace(['==', '='], ['2','1'] , $encodeString);
        } else {
            $encodeString .= '0';
        }

        return $encodeString;
    }

    /**
     * @internal
     * @param string $baseString
     * @return string
     */
    private static function decodeSpecialMap(string $baseString): string
    {
        $originStrings = [];
        $splitParts = array_filter(explode(':', $baseString));
        foreach ($splitParts as $part) {
            list($subDataPart, $offset) = str_split($part, strlen($part) - 1);
            $originStrings[] = str_pad($subDataPart, strlen($subDataPart) + (int)$offset, '=');
        }
        return str_replace(self::$charRemap, array_keys(self::$charRemap), implode(':', $originStrings));
    }

    /**
     * @internal
     * @return string
     */
    private static function getInitializationVector(): string
    {
        $vectorLen = openssl_cipher_iv_length(self::$CYPHER);
        return openssl_random_pseudo_bytes($vectorLen);
    }

    /**
     * @internal
     * @param string $tag
     * @param string $vector
     * @return string
     */
    private static function maskTagAndVector(string $tag, string $vector): string
    {
        $pointer = self::getMaskInsertPointer();
        $data = substr_replace($vector, $tag, $pointer, 0);
        return base64_encode($data);
    }

    /**
     * @internal
     * @param string $base64Data
     * @return array
     */
    private static function unmaskTagAndVector(string $base64Data): array
    {
        $pointer = self::getMaskInsertPointer();
        $data = base64_decode($base64Data);
        $vector = substr_replace($data, '', $pointer, self::$TAG_LEN);
        $offset = -abs(strlen($data) - self::$TAG_LEN - $pointer);
        $tag = substr($data, $pointer, $offset);

        return ['iv' => $vector, 'tag' => $tag];
    }

    /**
     * @internal
     * @return int
     */
    private static function getMaskInsertPointer(): int
    {
        $insertPointer = 0;
        $vectorLen = openssl_cipher_iv_length(self::$CYPHER);
        if ($vectorLen > self::$TAG_LEN) {
            $insertPointer = $vectorLen - self::$TAG_LEN;
        }

        return $insertPointer;
    }
}
