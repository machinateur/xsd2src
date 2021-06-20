<?php

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
