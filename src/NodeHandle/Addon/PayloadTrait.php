<?php


namespace App\NodeHandle\Addon;

use DOMAttr;
use DOMElement;

/**
 * Trait PayloadTrait
 * @package App\NodeHandle\Addon
 */
trait PayloadTrait
{
    /**
     * @param DOMElement $element
     * @return array
     */
    protected function payload(DOMElement $element): array
    {
        $payload = [];

        if ($element->hasAttributes()) {
            /** @var DOMAttr[] $attributeList */
            $attributeList = iterator_to_array($element->attributes, false);
            foreach ($attributeList as $attribute) {
                $payload[$attribute->name] = $attribute->value;
            }
        }

        return $payload;
    }
}
