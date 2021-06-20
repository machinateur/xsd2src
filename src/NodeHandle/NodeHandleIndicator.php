<?php

namespace App\NodeHandle;

use App\Model\Data\Content;
use DOMElement;

/**
 * Class NodeHandleIndicator
 * @package App\NodeHandle
 * @link https://www.w3schools.com/xml/el_all.asp
 * @link https://www.w3schools.com/xml/el_choice.asp
 * @link https://www.w3schools.com/xml/el_sequence.asp
 */
class NodeHandleIndicator extends NodeHandleAbstract
{
    protected static ?string $identifier = 'indicator';

    /**
     * @inheritDoc
     */
    public function accept(DOMElement $element, ?object $subject = null): bool
    {
        // TODO: Support more.
        return ('all' === $element->localName || 'choice' === $element->localName || 'sequence' === $element->localName)
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
