<?php


namespace App\Service\Dump;

use App\Model\Data\Content;

/**
 * Interface DumpInterface
 * @package App\Service\Dump
 */
interface DumpInterface
{
    public function begin(): void;

    /**
     * @param Content\Type $type
     * @param string $source
     * @return bool
     */
    public function dump(Content\Type $type, string $source): bool;

    public function end(): void;
}
