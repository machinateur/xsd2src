<?php

namespace App\NodeHandle;

use App\Model\Data\Content;
use DOMElement;

/**
 * Class NodeHandleSimpleType
 * @package App\NodeHandle
 * @link https://www.w3schools.com/xml/el_simpletype.asp
 */
class NodeHandleSimpleType extends NodeHandleAbstract
{
    protected static ?string $identifier = 'simple_type';

    /**
     * @inheritDoc
     */
    public function accept(DOMElement $element, ?object $subject = null): bool
    {
        return ('simpleType' === $element->localName)
            && ($subject instanceof Content || $subject instanceof Content\Type);
    }

    /**
     * @inheritDoc
     */
    public function handle(DOMElement $element, ?object $subject = null): void
    {
        switch (true) {

            // If coming from schema...
            case ($subject instanceof Content):
                $this->handleContent($element, $subject);
                break;

            // If coming from element...
            case ($subject instanceof Content\Type):
                $this->handleContentType($element, $subject);
                break;

        }
    }

    /**
     * @param DOMElement $element
     * @param Content $subject
     */
    protected function handleContent(DOMElement $element, Content $subject): void
    {
        $name = $element->getAttribute('name');

        $modelType = new Content\TypeProxy($name, true);
        $modelType->setWithContent(true);
        $modelType->setContentReference($subject);

        $this->getNodeHandleChain()
            ->handleAll($element, $modelType);

        $modelType->setContentReference(null);

        $subject->addType($modelType);
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
