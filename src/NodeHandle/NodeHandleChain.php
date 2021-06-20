<?php

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
