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

use DOMElement;

/**
 * Class NodeHandleChain
 * @package App\NodeHandle
 * @internal Wrap a list of `NodeHandle`s.
 */
final class NodeHandleChain implements NodeHandleInterface
{
    /**
     * @var NodeHandleInterface[]
     */
    private array $nodeHandleList;

    private bool $alwaysAccept;

    /**
     * NodeHandleChain constructor.
     * @param bool $alwaysAccept
     */
    public function __construct(bool $alwaysAccept = true)
    {
        $this->nodeHandleList = [];
        $this->alwaysAccept = $alwaysAccept;
    }

    /**
     * @param NodeHandleInterface $nodeHandle
     * @param bool $force
     * @return NodeHandleChain
     */
    public function addNodeHandle(NodeHandleInterface $nodeHandle, bool $force = false): NodeHandleChain
    {
        // TODO: Check self-reference!

        if ($nodeHandle instanceof NodeHandleAbstract && (!$nodeHandle->hasNodeHandleChain() || $force)) {
            $nodeHandle->setNodeHandleChain($this);
        }

        $this->nodeHandleList[$nodeHandle->getIdentifier()] = $nodeHandle;

        return $this;
    }

    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////

    /**
     * @inheritDoc
     */
    public function getIdentifier(): string
    {
        return 'chain';
    }

    /**
     * @inheritDoc
     */
    public function accept(DOMElement $element, ?object $subject = null): bool
    {
        /** @var NodeHandleInterface $nodeHandle */
        foreach ($this->nodeHandleList as $nodeHandle) {
            if ($nodeHandle->accept($element, $subject)) {
                return true;
            }
        }

        return $this->alwaysAccept;
    }

    /**
     * @inheritDoc
     */
    public function handle(DOMElement $element, ?object $subject = null): void
    {
        /** @var NodeHandleInterface $nodeHandle */
        foreach ($this->nodeHandleList as $nodeHandle) {
            if ($nodeHandle->accept($element, $subject)) {
                $nodeHandle->handle($element, $subject);
            }
        }
    }

    /**
     * @param DOMElement $element
     * @param object|null $subject
     */
    public function handleAll(DOMElement $element, ?object $subject = null): void
    {
        /** @var DOMElement $childNode */
        foreach ($element->childNodes as $childNode) {
            if (!$childNode instanceof DOMElement) {
                continue;
            }

            $this->handle($childNode, $subject);
        }
    }
}
