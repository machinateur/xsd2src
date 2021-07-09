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

/**
 * Class NodeHandleComplexType
 * @package App\NodeHandle
 * @link https://www.w3schools.com/xml/el_complextype.asp
 */
class NodeHandleComplexType extends NodeHandleAbstract
{
    protected static ?string $identifier = 'complex_type';

    /**
     * @inheritDoc
     */
    public function accept(DOMElement $element, ?object $subject = null): bool
    {
        return ('complexType' === $element->localName)
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

        $modelType = new Content\TypeProxy($name, false);
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
        $this->getNodeHandleChain()
            ->handleAll($element, $subject);
    }
}
