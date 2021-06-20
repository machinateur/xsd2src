<?php

namespace App\Model\Data\Content;

use App\Model\Data\Content;

/**
 * Class XsdAdapter
 * @package App\Model
 */
class TypeProxy extends Type
{
    private ?Content $contentReference;

    /**
     * @inheritDoc
     */
    public function __construct(string $name, bool $root = false)
    {
        parent::__construct($name, $root);
        $this->contentReference = null;
    }

    /**
     * @return Content|null
     */
    public function getContentReference(): ?Content
    {
        return $this->contentReference;
    }

    /**
     * @param Content|null $contentReference
     * @return TypeProxy
     */
    public function setContentReference(?Content $contentReference): TypeProxy
    {
        $this->contentReference = $contentReference;
        return $this;
    }

    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////

    /**
     * @return bool
     */
    public function hasContentReference(): bool
    {
        return null !== $this->contentReference;
    }
}
