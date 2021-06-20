<?php


namespace App\Service\Dump;

use App\Model\Data\Content;
use LogicException;

/**
 * Class DumpStream
 * @package App\Service\Dump
 */
class DumpStream implements DumpInterface
{
    /**
     * @var resource
     */
    protected $stream;

    /**
     * DumpStream constructor.
     * @param resource $stream
     */
    public function __construct(/*resource*/ $stream)
    {
        $this->stream = $stream;
    }

    public function begin(): void
    {
        if (!is_resource($this->stream)) {
            $metaData = stream_get_meta_data($this->stream);

            if (!in_array($metaData['mode'], [
                'w',
            ])) {
                throw new LogicException('The stream must be a resource and writable.');
            }
        }
    }

    /**
     * @inheritDoc
     */
    public function dump(Content\Type $type, string $source): bool
    {
        return false !== fwrite($this->stream, $source, strlen($source));
    }

    public function end(): void
    {
        rewind($this->stream);
    }
}
