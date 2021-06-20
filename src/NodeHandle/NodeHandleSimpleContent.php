<?php

namespace App\NodeHandle;

use App\Model\Data\Content;
use DOMElement;

/**
 * Class NodeHandleSimpleContent
 * @package App\NodeHandle
 * @link https://www.w3schools.com/xml/el_simplecontent.asp
 */
class NodeHandleSimpleContent extends NodeHandleAbstract
{
    protected static ?string $identifier = 'simple_content';

    /**
     * @inheritDoc
     */
    public function accept(DOMElement $element, ?object $subject = null): bool
    {
        return ('simpleContent' === $element->localName)
            && ($subject instanceof Content\Type);
    }

    /**
     * @inheritDoc
     */
    public function handle(DOMElement $element, ?object $subject = null): void
    {
        switch (true) {

            // If coming from element...
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
        $subject->setWithContent(true);

        $this->getNodeHandleChain()
            ->handleAll($element, $subject);
    }
}
