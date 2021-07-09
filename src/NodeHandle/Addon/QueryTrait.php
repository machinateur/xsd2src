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

namespace App\NodeHandle\Addon;

use DOMDocument;
use DOMElement;
use DOMNodeList;
use DOMXPath;
use LogicException;

/**
 * Trait XPathTrait
 * @package App\NodeHandle\Addon
 */
trait QueryTrait
{
    /**
     * @param DOMElement $element
     * @param string $expression
     * @param bool $relative
     * @return DOMNodeList
     */
    protected function query(DOMElement $element, string $expression, bool $relative = false): DOMNodeList
    {
        $xPath = $this->getXPath($element);

        $contextNode = $relative
            ? $element
            : null;

        $result = $xPath->query($expression, $contextNode);

        if (false === $result) {
            throw new LogicException('The expression must not be malformed!');
        }

        return $result;
    }

    /**
     * @param DOMElement $element
     * @return DOMXPath
     */
    private function getXPath(DOMElement $element): DOMXPath
    {
        /** @var DOMDocument $document */
        $document = $element->ownerDocument;

        return new DOMXPath($document);
    }
}
