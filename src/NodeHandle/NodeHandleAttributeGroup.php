<?php

namespace App\NodeHandle;

use App\Model\Data\Content;
use DOMElement;
use LogicException;

/**
 * Class NodeHandleAttributeGroup
 * @package App\NodeHandle
 * @see https://www.w3schools.com/xml/el_attributegroup.asp
 */
class NodeHandleAttributeGroup extends NodeHandleAbstract
{
    use Addon\QueryTrait;

    protected static ?string $identifier = 'attribute_group';

    private ?Content $contentCache;

    /**
     * NodeHandleAttributeGroup constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->contentCache = null;
    }

    /**
     * @return Content
     */
    public function getContentCache(): Content
    {
        if (!$this->hasContentCache()) {
            $this->setContentCache(
                new Content()
            );
        }

        return $this->contentCache;
    }

    /**
     * @param Content $contentCache
     * @return NodeHandleAttributeGroup
     */
    public function setContentCache(Content $contentCache): NodeHandleAttributeGroup
    {
        $this->contentCache = $contentCache;
        return $this;
    }

    /**
     * @return bool
     */
    public function hasContentCache(): bool
    {
        return null !== $this->contentCache;
    }

    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////

    /**
     * @inheritDoc
     */
    public function accept(DOMElement $element, ?object $subject = null): bool
    {
        return ('attributeGroup' === $element->localName)
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
     * @param Content $subject
     */
    protected function handleContent(DOMElement $element, Content $subject): void
    {
        $name = $element->getAttribute('name');

        $modelContent = $this->getContentCache();

        $modelType = new Content\TypeProxy($name, true);
        $modelType->setContentReference($subject);

        $this->getNodeHandleChain()
            ->handleAll($element, $modelType);

        $modelType->setContentReference(null);

        $modelContent->addType($modelType);
    }

    /**
     * @param DOMElement $element
     * @param Content\Type $subject
     */
    protected function handleContentType(DOMElement $element, Content\Type $subject): void
    {
        $ref = $element->getAttribute('ref');

        $contentCache = $this->getContentCache();

        if ($contentCache->hasTypeByName($ref)) {
            $modelType = $contentCache->getTypeByName($ref);

            $attributeList = $modelType->getAttributeList();
            foreach ($attributeList as $attributeItem) {
                // Pass the same instance...
                $subject->addAttribute($attributeItem);
            }
        } else {
            $this->locateAndHandleContent($element, $subject, $ref);
        }
    }

    /**
     * @param DOMElement $element
     * @param Content\Type $subject
     * @param string $ref
     */
    protected function locateAndHandleContent(DOMElement $element, Content\Type $subject, string $ref)
    {
        $nodeList = $this->query($element, "/*[local-name()='schema']/*[local-name()='attributeGroup'][@name='{$ref}']", false);

        if (1 === $nodeList->length) {
            $node = $nodeList->item(0);

            if ($node instanceof DOMElement) {
                $this->handleContent($node, $this->getContentCache());

                // Retry with cache...
                $this->handleContentType($element, $subject);
            }
        } else {
            throw new LogicException('The attribute group name must be unique inside the schema element.');
        }
    }
}
