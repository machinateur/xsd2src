<?php


namespace App\Service\Dump;

use App\Model\Data\Content;
use Closure;

/**
 * Class DumpDirectory
 * @package App\Service\Dump
 */
class DumpDirectory implements DumpInterface
{
    protected string $path;

    protected Closure $nameFactory;

    /**
     * DumpDirectory constructor.
     * @param string $path
     * @param Closure $nameFactory
     */
    public function __construct(string $path, Closure $nameFactory)
    {
        $this->path = $path;
        $this->nameFactory = $nameFactory;
    }

    public function begin(): void
    {
    }

    /**
     * @inheritDoc
     */
    public function dump(Content\Type $type, string $source): bool
    {
        $dump = new DumpFile($this->getNameFactory());
        $dump->begin();
        $result = $dump->dump($type, $source);
        $dump->end();

        return $result;
    }

    public function end(): void
    {
    }

    /**
     * @return Closure
     */
    protected function getNameFactory(): Closure
    {
        return function (Content\Type $type): string {
            return $this->path . '/' . ($this->nameFactory)($type);
        };
    }
}