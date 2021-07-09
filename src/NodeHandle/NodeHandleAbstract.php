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
use LogicException;

/**
 * Class NodeHandleAbstract
 * @package App\NodeHandle
 */
abstract class NodeHandleAbstract implements NodeHandleInterface
{
    protected static ?string $identifier = null;

    private ?NodeHandleChain $nodeHandleChain;

    /**
     * NodeHandleAbstract constructor.
     */
    public function __construct()
    {
        $this->nodeHandleChain = null;
    }

    /**
     * @return NodeHandleChain
     */
    public function getNodeHandleChain(): NodeHandleChain
    {
        if (!$this->hasNodeHandleChain()) {
            throw new LogicException('The node handle chain must not be "null".');
        }

        return $this->nodeHandleChain;
    }

    /**
     * @param NodeHandleChain $nodeHandleChain
     * @return NodeHandleAbstract
     */
    public function setNodeHandleChain(NodeHandleChain $nodeHandleChain): NodeHandleAbstract
    {
        $this->nodeHandleChain = $nodeHandleChain;
        return $this;
    }

    /**
     * @return bool
     */
    public function hasNodeHandleChain(): bool
    {
        return null !== $this->nodeHandleChain;
    }

    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////

    /**
     * @inheritDoc
     */
    public function getIdentifier(): string
    {
        $identifier = static::$identifier;

        if (null === $identifier) {
            throw new LogicException('The identifier must not be "null.');
        }

        return $identifier;
    }

    /**
     * @inheritDoc
     */
    public abstract function accept(DOMElement $element, ?object $subject = null): bool;

    /**
     * @inheritDoc
     */
    public abstract function handle(DOMElement $element, ?object $subject = null): void;
}
