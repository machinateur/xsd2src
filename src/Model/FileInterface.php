<?php

namespace App\Model;

use DOMDocument;

/**
 * Interface FileInterface
 * @package App\Model
 */
interface FileInterface
{
    /**
     * @return DOMDocument
     */
    public function getDocument(): DOMDocument;
}
