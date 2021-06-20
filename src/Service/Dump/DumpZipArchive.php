<?php


namespace App\Service\Dump;

use App\Model\Data\Content;
use Closure;
use ZipArchive;

/**
 * Class DumpZipArchive
 * @package App\Service\Dump
 */
class DumpZipArchive implements DumpInterface
{
    protected string $path;

    protected Closure $nameFactory;

    protected string $filename;

    protected ?ZipArchive $archive;

    /**
     * DumpZipArchive constructor.
     * @param string $path
     * @param Closure $nameFactory
     * @param string $filename
     */
    public function __construct(string $path, Closure $nameFactory, string $filename)
    {
        $this->path = $path;
        $this->nameFactory = $nameFactory;
        $this->filename = $filename;
        $this->archive = null;
    }

    public function begin(): void
    {
        $this->archive = new ZipArchive();

        $this->archive->open($this->path . '/' . $this->filename, ZipArchive::CREATE | ZipArchive::OVERWRITE);
    }

    /**
     * @inheritDoc
     */
    public function dump(Content\Type $type, string $source): bool
    {
        $pathname = ($this->getNameFactory())($type);
        $path = dirname($pathname);

        // First ensure the path exists...
        $this->archive->addEmptyDir($path);

        return $this->archive->addFromString($pathname, $source);
    }

    public function end(): void
    {
        $this->archive->close();

        $this->archive = null;
    }

    /**
     * @return Closure
     */
    protected function getNameFactory(): Closure
    {
        return $this->nameFactory;
    }
}
