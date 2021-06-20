<?php

namespace App\NodeHandle;

use App\Model\Data\Content;
use DOMElement;

/**
 * Class NodeHandleSchema
 * @package App\NodeHandle
 * @link https://www.w3schools.com/xml/el_schema.asp
 */
class NodeHandleSchema extends NodeHandleAbstract
{
    protected static ?string $identifier = 'schema';

    /**
     * @inheritDoc
     */
    public function accept(DOMElement $element, ?object $subject = null): bool
    {
        return ('schema' === $element->localName)
            && ($subject instanceof Content);
    }

    /**
     * @inheritDoc
     */
    public function handle(DOMElement $element, ?object $subject = null): void
    {
        $this->getNodeHandleChain()
            ->handleAll($element, $subject);
    }
}
