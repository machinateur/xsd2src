<?php

namespace App\NodeHandle;

use App\Model\Data\Content;
use DOMElement;

/**
 * Class NodeHandleRestriction
 * @package App\NodeHandle
 * @link https://www.w3schools.com/xml/el_restriction.asp
 */
class NodeHandleRestriction extends NodeHandleAbstract
{
    protected static ?string $identifier = 'restriction';

    /**
     * @inheritDoc
     */
    public function accept(DOMElement $element, ?object $subject = null): bool
    {
        return ('restriction' === $element->localName)
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

        // Look for restricted elements inside using a substitute $subject. Then remove the restricted elements.
        $modelContent = new Content();

        $modelType = new Content\TypeProxy($subject->getName(), $subject->isRoot());
        $modelType->setParent($subject->getParent());
        $modelType->setWithContent($subject->isWithContent());
        $modelType->setContentReference($modelContent);

        $this->getNodeHandleChain()
            ->handleAll($element, $modelType);

        $modelType->setContentReference(null);

        $modelAttributeList = $modelType->getAttributeList();
        foreach ($modelAttributeList as $modelAttribute) {
            if (!$subject->hasAttribute($modelAttribute)) {
                $subject->removeAttribute($modelAttribute);
            }
        }

        $modelElementList = $modelType->getElementList();
        foreach ($modelElementList as $modelElement) {
            if (!$subject->hasElement($modelElement)) {
                $subject->removeElement($modelElement);
            }
        }
    }
}
