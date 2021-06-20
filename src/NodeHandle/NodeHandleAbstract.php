<?php

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
