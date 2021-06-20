<?php

namespace App\Service\Scan;

use App\Model\Data\Content;
use DOMDocument;

/**
 * Interface ScanInterface
 * @package App\Service\Scan
 */
interface ScanInterface
{
    /**
     * @param Content $content
     * @param DOMDocument $document
     * @param string $documentElementName
     * @return Content
     */
    public function run(Content $content, DOMDocument $document, string $documentElementName = 'schema'): Content;
}
