<?php

namespace App\TypeMap;

use App\Model\Data\Content;

/**
 * Interface TypeMapInterface
 * @package App\TypeMap
 */
interface TypeMapInterface
{
    /**
     * @param string $typeFrom
     * @param string $typeTo
     * @return TypeMapInterface
     */
    public function entry(string $typeFrom, string $typeTo): TypeMapInterface;

    /**
     * @param Content $content
     * @return TypeMapInterface
     */
    public function process(Content $content): TypeMapInterface;
}
