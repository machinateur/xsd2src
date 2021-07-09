<?php


namespace App\Model;

use DOMDocument;
use LogicException;

/**
 * Class FileWithSchema
 * @package App\Model
 */
class FileWithSchema extends File
{
    private string $schemaPathname;

    /**
     * @inheritDoc
     */
    public function __construct(string $pathname, string $schemaPathname)
    {
        parent::__construct($pathname);
        $this->schemaPathname = $schemaPathname;
    }

    /**
     * @return string
     */
    public function getSchemaPathname(): string
    {
        return $this->schemaPathname;
    }

    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////

    /**
     * @inheritDoc
     */
    public function getDocument(): DOMDocument
    {
        $document = parent::getDocument();

        $schemaPathname = $this->getSchemaPathname();

        if (!$document->schemaValidate($schemaPathname)) {
            throw new LogicException('The document must be a valid xsd file.');
        }

        return $document;
    }

    protected function validate(): void
    {
        parent::validate();

        $schemaPathname = $this->getSchemaPathname();

        if (!(file_exists($schemaPathname) && is_file($schemaPathname))) {
            throw new LogicException('The schema pathname must be a file.');
        }
    }
}
