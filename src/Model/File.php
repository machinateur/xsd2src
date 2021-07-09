<?php

namespace App\Model;

use DOMDocument;
use LogicException;

/**
 * Class File
 * @package App\Model
 */
class File implements FileInterface
{
    private string $pathname;

    /**
     * XsdFile constructor.
     * @param string $pathname
     */
    public function __construct(string $pathname)
    {
        $this->pathname = $pathname;
    }

    /**
     * @return string
     */
    public function getPathname(): string
    {
        return $this->pathname;
    }

    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////

    /**
     * @inheritDoc
     */
    public function getDocument(): DOMDocument
    {
        $this->validate();

        $pathname = $this->getPathname();

        $document = new DOMDocument('1.0', 'utf-8');
        if (!$document->load($pathname)) {
            throw new LogicException('The pathname must be a valid xsd file.');
        }

        return $document;
    }

    protected function validate(): void
    {
        $pathname = $this->getPathname();

        if (!(file_exists($pathname) && is_file($pathname))) {
            throw new LogicException('The pathname must be a file.');
        }
    }
}
