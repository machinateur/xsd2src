<?php

namespace App\NodeHandle;

use App\Model\Data\Content;
use DOMElement;

/**
 * Class NodeHandleExtension
 * @package App\NodeHandle
 * @link https://www.w3schools.com/xml/el_extension.asp
 */
class NodeHandleExtension extends NodeHandleAbstract
{
    protected static ?string $identifier = 'extension';

    /**
     * @inheritDoc
     */
    public function accept(DOMElement $element, ?object $subject = null): bool
    {
        return ('extension' === $element->localName)
            && ($subject instanceof Content\Type);
    }

    /**
     * @inheritDoc
     */
    public function handle(DOMElement $element, ?object $subject = null): void
    {
        switch (true) {

            // If coming from simpleContent/complexContent...
            case ($subject instanceof Content\Type):
                $this->handleContentType($element, $subject);
                break;

        }
    }

    /**
     * @param DOMElement $element
     * @param Content\Type $subject
     */
    protected function handleContentType(DOMElement $element, Content\Type $subject): void
    {
        $base = $element->getAttribute('base');

        $subject->setParent($base);

        $this->getNodeHandleChain()
            ->handleAll($element, $subject);
    }
}
