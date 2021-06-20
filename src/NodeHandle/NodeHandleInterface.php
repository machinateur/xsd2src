<?php

namespace App\NodeHandle;

use DOMElement;

/**
 * Interface NodeHandleInterface
 * @package App\NodeHandle
 */
interface NodeHandleInterface
{
    /**
     * @return string
     */
    public function getIdentifier(): string;

    /**
     * @param DOMElement $element
     * @param object|null $subject
     * @return bool
     */
    public function accept(DOMElement $element, ?object $subject = null): bool;

    /**
     * @param DOMElement $element
     * @param object|null $subject
     */
    public function handle(DOMElement $element, ?object $subject = null): void;
}
