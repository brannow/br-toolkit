<?php

namespace BR\Toolkit\Misc\Native;

class FileHandler
{
    public function exists(string $filename): bool
    {
        return file_exists($filename);
    }

    public function require(string $filename)
    {
        return require $filename;
    }

    public function requireOnce(string $filename)
    {
        return require_once $filename;
    }

    public function include(string $filename)
    {
        return include $filename;
    }

    public function includeOnce(string $filename)
    {
        return include_once $filename;
    }
}