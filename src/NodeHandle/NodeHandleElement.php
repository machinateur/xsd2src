<?php

namespace App\NodeHandle;

use App\Model\Data\Content;
use DOMElement;
use LogicException;

/**
 * Class NodeHandleElement
 * @package App\NodeHandle
 * @link https://www.w3schools.com/xml/el_element.asp
 */
class NodeHandleElement extends NodeHandleAbstract
{
    use Addon\PayloadTrait;

    protected static ?string $identifier = 'element';

    /**
     * @inheritDoc
     */
    public function accept(DOMElement $element, ?object $subject = null): bool
    {
        return ('element' === $element->localName)
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
        $modelType->setContentReference($subject);

        if ($element->hasAttribute('type')) {
            // If type is set as attribute...

            $type = $element->getAttribute('type');

            $modelType->setParent($type);
        } else {
            // If type is not set as attribute...

            $this->getNodeHandleChain()
                ->handleAll($element, $modelType);
        }

        $modelType->setContentReference(null);

        $subject->addType($modelType);
    }

    /**
     * @param DOMElement $element
     * @param Content\Type $subject
     */
    protected function handleContentType(DOMElement $element, Content\Type $subject): void
    {
        $name = $element->getAttribute('name');

        $required = 1 === ($element->hasAttribute('minOccurs')
                ? (int)$element->getAttribute('minOccurs')
                : 1
            );
        $singular = 1 === ($element->hasAttribute('maxOccurs')
                ? (int)$element->getAttribute('maxOccurs')
                : 1
            );

        if ($element->hasAttribute('type')) {
            // If type is set as attribute...

            $type = $element->getAttribute('type');

            $modelElement = new Content\Type\Element($name, $type, $required, $singular);
        } else {
            // If type is not set as attribute...

            $type = $subject->getName() . ucfirst($name);

            $modelElement = new Content\Type\Element($name, $type, $required, $singular);

            if ($subject instanceof Content\TypeProxy && $subject->hasContentReference()) {
                $modelType = new Content\TypeProxy($type, false);
                $modelType->setContentReference($subject->getContentReference());

                $this->getNodeHandleChain()
                    ->handleAll($element, $modelType);

                $modelType->setContentReference(null);

                $subject->getContentReference()
                    ->addType($modelType);
            } else {
                throw new LogicException('The subject must be a "Content\TypeProxy" and hold content!');
            }
        }

        $payload = $this->payload($element);
        $modelElement->setPayload($payload);

        $subject->addElement($modelElement);
    }
}
