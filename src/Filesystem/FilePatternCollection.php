<?php

namespace Vanengers\Copy\Filesystem;

class FilePatternCollection
{
    /**
     * @var string[]
     */
    public array $folders = [];

    /**
     * @var string[]
     */
    public array $files = [];

    public function __construct(array $folders = [], array $files = [])
    {
        $this->folders = $folders;
        $this->files = $files;
    }

    /**
     * @param array $data
     * @return FilePatternCollection
     */
    public static function filterPatterns(array $data = []) : self
    {
        $folders = [];
        $files = [];
        foreach ($data as $key => $value) {
            if (self::hasExtension($value)) {
                $files[] = $value;
            } else {
                $folders[] = $value;
            }
        }

        return new self($folders, $files);
    }

    private static function hasExtension($value)
    {
        return strpos($value, '.') !== false;
    }
}