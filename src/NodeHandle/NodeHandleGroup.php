<?php
/*
 * MIT License
 *
 * Copyright (c) 2021-2021 machinateur
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 * SOFTWARE.
 */

namespace App\NodeHandle;

use App\Model\Data\Content;
use DOMElement;
use LogicException;

/**
 * Class NodeHandleGroup
 * @package App\NodeHandle
 * @see https://www.w3schools.com/xml/el_group.asp
 */
class NodeHandleGroup extends NodeHandleAbstract
{
    use Addon\QueryTrait;
    use Addon\ContentCacheTrait;

    protected static ?string $identifier = 'group';

    /**
     * @inheritDoc
     */
    public function accept(DOMElement $element, ?object $subject = null): bool
    {
        return ('group' === $element->localName)
            && ($subject instanceof Content\Type);
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

        $modelContent = $this->getContentCache();

        $modelType = new Content\TypeProxy($name, true);
        $modelType->setContentReference($subject);

        $this->getNodeHandleChain()
            ->handleAll($element, $modelType);

        $modelType->setContentReference(null);

        // Use the cache instead of the subject.
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

            $elementList = $modelType->getElementList();
            foreach ($elementList as $elementItem) {
                // Pass the same instance...
                $subject->addElement($elementItem);
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
        $nodeList = $this->query($element, "/*[local-name()='schema']/*[local-name()='group'][@name='{$ref}']", false);

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
