<?php

namespace App\NodeHandle;

use App\Model\Data\Content;
use DOMElement;
use LogicException;

/**
 * Class NodeHandleAttribute
 * @package App\NodeHandle
 * @see https://www.w3schools.com/xml/el_attribute.asp
 */
class NodeHandleAttribute extends NodeHandleAbstract
{
    use Addon\PayloadTrait;

    protected static ?string $identifier = 'attribute';

    /**
     * @inheritDoc
     */
    public function accept(DOMElement $element, ?object $subject = null): bool
    {
        return ('attribute' === $element->localName)
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
        $name = $element->getAttribute('name');

        $required = 'required' === ($element->hasAttribute('use')
                ? $element->getAttribute('use')
                : 'optional'
            );

        if ($element->hasAttribute('type')) {
            // If type is set as attribute...

            $type = $element->getAttribute('type');

            $modelAttribute = new Content\Type\Attribute($name, $type, $required);
        } else {
            // If type is not set as attribute...

            $type = $subject->getName() . ucfirst($name);

            $modelAttribute = new Content\Type\Attribute($name, $type, $required);

            if ($subject instanceof Content\TypeProxy && $subject->hasContentReference()) {
                $modelType = new Content\TypeProxy($type, false);
                $modelType->setContentReference($subject->getContentReference());

                $this->getNodeHandleChain()
                    ->handleAll($element, $modelType);

                $modelType->setContentReference(null);

                $subject->getContentReference()
                    ->addType($modelType);
            } else {
                throw new LogicException('The subject must be a "Content\TypeProxy" and hold content.');
            }
        }

        $payload = $this->payload($element);
        $modelAttribute->setPayload($payload);

        $subject->addAttribute($modelAttribute);
    }
}
