<?php


namespace App\NodeHandle\Addon;

use App\Model\Data\Content;

/**
 * Trait ContentCacheTrait
 * @package App\NodeHandle\Addon
 */
trait ContentCacheTrait
{
    private ?Content $contentCache = null;

    /**
     * @return Content
     */
    public function getContentCache(): Content
    {
        if (!$this->hasContentCache()) {
            $this->contentCache = new Content();
        }

        return $this->contentCache;
    }

    /**
     * @return bool
     */
    public function hasContentCache(): bool
    {
        return null !== $this->contentCache;
    }
}