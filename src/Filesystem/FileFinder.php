<?php

namespace Vanengers\Copy\Filesystem;

use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

class FileFinder
{
    private $path = '';

    /** @var array exclude_patters */
    private array $exclude_patters;
    /** @var mixed|true ignoreDotFiles */
    private $ignoreDotFiles;
    /** @var mixed|true ignoreVCS */
    private $ignoreVCS;
    /** @var mixed|true ignoreComposerFiles */
    private $ignoreComposerFiles;


    public function __construct($path, array $exclude_patters = [], $ignoreDotFiles = true, $ignoreVCS = true,
                $ignoreComposerFiles = true)
    {
        $this->path = $path;
        $this->exclude_patters = $exclude_patters;
        $this->ignoreDotFiles = $ignoreDotFiles;
        $this->ignoreVCS = $ignoreVCS;
        $this->ignoreComposerFiles = $ignoreComposerFiles;
    }

    /**
     * @param $path
     * @return Finder
     */
    public function findFiles($path) : Finder
    {
        $patterns = FilePatternCollection::filterPatterns($this->exclude_patters);

        $finder = new Finder();
        $found = $finder
            ->in($path instanceof SplFileInfo ? $path->getRealPath() : $path)
            ->ignoreDotFiles($this->ignoreDotFiles)
            ->ignoreVCS($this->ignoreVCS);

        if ($this->ignoreComposerFiles) {
            $found->notPath('composer.json');
            $found->notPath('composer.lock');
        }

        if (count($patterns->folders) > 0) {
            $finder->notPath($patterns->folders);
        }

        if (count($patterns->files) > 0) {
            $finder->exclude($patterns->files);
        }

        if ($path instanceof SplFileInfo) {
            // we probably are in symlinked composer package, so lets skip the vendor folder
            $found = $found->exclude('vendor');
        }

        return $found->files();
    }
}