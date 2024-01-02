<?php

namespace Vanengers\Copy\Filesystem;

use SplFileInfo;
use Symfony\Component\Filesystem\Filesystem;
use Throwable;

class FileCopier
{
    /** @var Filesystem filesystem */
    private Filesystem $fs;
    /** @var string toCopyPathFolder */
    private string $toCopyPathFolder;
    /** @var FileFinder finder */
    private FileFinder $finder;

    public function __construct(Filesystem $filesystem, string $destinationPath, FileFinder $finder)
    {
        $this->fs = $filesystem;
        $this->toCopyPathFolder = $destinationPath;
        $this->finder = $finder;
    }

    /**
     * @param SplFileInfo $file
     * @param string $relativePath
     * @return void
     */
    public function copyFile(SplFileInfo $file, string $relativePath = '')
    {
        if ($file->isDir()) {
            $this->fs->mkdir($this->toCopyPathFolder . '\\' . $relativePath . '\\' . $file->getRelativePathname());
        }
        else if ($this->isSymlink($file) && empty($relativePath)) {
            $nPath = $this->toCopyPathFolder . '\\' . $relativePath . '\\' .$file->getRelativePath();
            $rel = $file->getRelativePathname();
            $this->fs->mkdir($nPath);
            if (!$file->isFile() && !$file->isLink()) {
                $files = $this->finder->findFiles($file);
                foreach ($files as $file) {
                    $this->copyFile($file, $rel);
                }
            }
            else {
                $this->copy($file->getRealPath(), $this->toCopyPathFolder . '\\' . $relativePath . '\\' . $file->getRelativePathname());
            }
        }
        else {
            $this->copy($file->getRealPath(), $this->toCopyPathFolder . '\\' . $relativePath . '\\' . $file->getRelativePathname());
        }
    }

    private function copy($source, $destination)
    {
        $this->fs->copy($source, $destination);
    }

    private function isSymlink(SplFileInfo $file): bool
    {
        $absPathOrg = $this->toCopyPathFolder . '\\' .$file->getRelativePathname();
        return !str_contains($absPathOrg, $file->getRealPath());
    }
}