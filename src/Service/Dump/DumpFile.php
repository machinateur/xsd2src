<?php


namespace App\Service\Dump;

use App\Model\Data\Content;
use Closure;
use RuntimeException;

/**
 * Class DumpFile
 * @package App\Service\Dump
 */
class DumpFile implements DumpInterface
{
    protected Closure $nameFactory;

    /**
     * DumpFile constructor.
     * @param Closure $nameFactory
     */
    public function __construct(Closure $nameFactory)
    {
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
        $pathname = ($this->getNameFactory())($type);
        $path = dirname($pathname);

        if (!(file_exists($path) && is_dir($path)) && !mkdir(0755)) {
            throw new RuntimeException('Error: The path at "' . $path
                . '" must exist and could not be created automatically.');
        }

        /** @var resource $stream */
        $stream = fopen($pathname, 'w');

        $dump = new DumpStream($stream);
        $dump->begin();
        $result = $dump->dump($type, $source);
        $dump->end();

        fclose($stream);

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
        return $this->nameFactory;
    }
}
