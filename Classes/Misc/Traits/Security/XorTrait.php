<?php

namespace BR\Toolkit\Misc\Traits\Security;

trait XorTrait
{
    /*
     // JAVASCRIPT CODE
    function xor(input,key)
    {
        input = b64EncodeUnicode(input);
        let c = '';
        if (key === '' || key === null) {
        key = 'A';
        }
        while (key.length < input.length) {
        key += key;
        }

        for(let i=0; i<input.length; i++) {
        let value1 = input[i].charCodeAt(0);
        let value2 = key[i].charCodeAt(0);

        let xorValue = value1 ^ value2;
        let xorValueAsHexString = xorValue.toString('16');
        if (xorValueAsHexString.length < 2) {
        xorValueAsHexString = "0" + xorValueAsHexString;
        }

        c += xorValueAsHexString;
        }

        return c;
    },

    function b64EncodeUnicode(str)
    {
        return btoa(encodeURIComponent(str).replace(/%([0-9A-F]{2})/g,
        function toSolidBytes(match, p1) {
        return String.fromCharCode('0x' + p1);
        }));
    },
     */

    protected static function decryptJavascriptXor(string $data, string $key): string
    {
        return base64_decode(self::xor(self::hex2bin($data), $key));
    }

    private static function hex2bin(string $hexString): string
    {
        $output = '';
        foreach (str_split($hexString, 2) as $hexValue) {
            $binChar = hex2bin($hexValue);
            $output .= $binChar;
        }

        return $output;
    }

    protected static function encryptXor(string $data, string $key): string
    {
        return bin2hex(self::xor(base64_encode($data), $key));
    }

    protected static function decryptXor(string $data, string $key): string
    {
        return base64_decode(self::xor(hex2bin($data), $key));
    }

    private static function normalizeKey(string $data, string $key): string
    {
        $keyLen = strlen($key);
        $dataLen = strlen($data);

        if($keyLen === 0) {
            return str_pad('', $dataLen, 'A');
        }

        while ($keyLen < $dataLen) {
            $key = $key . $key;
            $keyLen += $keyLen;
        }

        return $key;
    }

    protected static function xor(string $payload, string $key): string
    {
        $k = self::normalizeKey($payload, $key);
        $dataLen = strlen($payload);
        $output = '';
        for($i=0; $i < $dataLen; ++$i) {
            $vP = $payload[$i];
            $kP = $k[$i];

            $vkP = $vP ^ $kP;
            $output .= $vkP;
        }

        return $output;
    }
}
