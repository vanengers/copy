<?php

namespace Vanengers\Copy\Command;


use SplFileInfo;
use Symfony\Component\Filesystem\Filesystem;
use Vanengers\Copy\Filesystem\FileCopier;
use Vanengers\Copy\Filesystem\FileFinder;
use Vanengers\SymfonyConsoleCommandLib\AbstractConsoleCommand;
use Vanengers\SymfonyConsoleCommandLib\Param\Option;

class CopyCommand extends AbstractConsoleCommand
{

    public $source;
    public $destination;
    public $excludes = [];
    public $ignoreDotFiles = true;
    public $ignoreVCS = true;
    public $ignoreComposer = true;

    private array $errors = [];

    /** @var ?Filesystem $fs */
    private $fs = null;

    /** @var FileFinder finder */
    private FileFinder $finder;

    public function executeCommand()
    {
        $this->io->title("Copying files from {$this->source} to {$this->destination}");
        if (count($this->excludes) > 0) {
            $this->io->writeln("Excluding: " . implode(', ', $this->excludes));
        }

        $this->fs = new Filesystem();
        $this->finder = new FileFinder($this->source, array_merge([$this->destination],$this->excludes),
            $this->ignoreDotFiles, $this->ignoreVCS, $this->ignoreComposer);

        $this->runCopy();

        if (count($this->errors) > 0) {
            $this->io->error("Errors:");
            foreach ($this->errors as $error) {
                $this->io->error($error);
            }
        } else {
            $this->io->success("Done! finished copying files without errors");
        }
    }

    public function getCommandName()
    {
        return "copy";
    }

    public function getCommandDescription()
    {
        return "Copy files from one directory to another";
    }

    public function getOptions()
    {
        return [
            new Option('source', 'source directory', 'string', null, true),
            new Option('destination', 'destination directory', 'string', null, true),
            new Option('excludes', 'files/folders to exclude', 'array', [], true),
            new Option('ignoreDotFiles', 'ignore dot files', 'bool', true, true),
            new Option('ignoreVCS', 'ignore version control files', 'bool', true, true),
            new Option('ignoreComposer', 'ignore version control files', 'bool', true, true),
        ];
    }

    private function runCopy()
    {
        $filesToCopy = $this->finder->findFiles($this->source);
        if (!$this->fs->exists($this->destination)) {
            $this->fs->mkdir($this->destination);
        }

        $zerox = new FileCopier($this->fs, $this->destination, $this->finder);
        foreach ($filesToCopy as $file) {
            $zerox->copyFile($file);
        }
    }
}