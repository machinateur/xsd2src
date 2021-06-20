<?php

namespace App\NodeHandle;

use App\Model\Data\Content;
use DOMElement;

/**
 * Class NodeHandleComplexContent
 * @package App\NodeHandle
 * @link https://www.w3schools.com/xml/el_complexcontent.asp
 */
class NodeHandleComplexContent extends NodeHandleAbstract
{
    protected static ?string $identifier = 'complex_content';

    /**
     * @inheritDoc
     */
    public function accept(DOMElement $element, ?object $subject = null): bool
    {
        return ('complexContent' === $element->localName)
            && ($subject instanceof Content\Type);
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
