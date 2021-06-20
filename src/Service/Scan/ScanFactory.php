<?php

namespace App\Service\Scan;

use App\NodeHandle\NodeHandleChain;
use App\NodeHandle\NodeHandleInterface;

/**
 * Class ScanFactory
 * @package App\Service\Scan
 */
final class ScanFactory
{
    /**
     * @return ScanFactory
     */
    public static function create(): ScanFactory
    {
        return new self();
    }

    private int $selectMode;

    private ?string $selectPattern;

    /**
     * @var NodeHandleInterface[]
     */
    private array $nodeHandleList;

    /**
     * ScanFactory constructor.
     */
    private function __construct()
    {
        $this->selectMode = Scan::SEL_ANY;
        $this->selectPattern = null;
        $this->nodeHandleList = [];
    }

    /**
     * @return int
     */
    public function getSelectMode(): int
    {
        return $this->selectMode;
    }

    /**
     * @param int $selectMode
     * @return ScanFactory
     */
    public function setSelectMode(int $selectMode): ScanFactory
    {
        $this->selectMode = $selectMode;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getSelectPattern(): ?string
    {
        return $this->selectPattern;
    }

    /**
     * @param string|null $selectPattern
     * @return ScanFactory
     */
    public function setSelectPattern(?string $selectPattern): ScanFactory
    {
        $this->selectPattern = $selectPattern;
        return $this;
    }

    /**
     * @return NodeHandleInterface[]|array
     */
    public function getNodeHandleList(): array
    {
        return $this->nodeHandleList;
    }

    /**
     * @param NodeHandleInterface[]|array $nodeHandleList
     * @return ScanFactory
     */
    public function setNodeHandleList(array $nodeHandleList): ScanFactory
    {
        $this->nodeHandleList = $nodeHandleList;
        return $this;
    }

    /**
     * @return Scan
     */
    public function make(): Scan
    {
        $nhChain = new NodeHandleChain(true);

        /** @var NodeHandleInterface $nodeHandle */
        foreach ($this->nodeHandleList as $nodeHandle) {
            $nhChain->addNodeHandle($nodeHandle, true);
        }

        return (new Scan($nhChain))
            ->setSelectMode($this->selectMode, $this->selectPattern);
    }
}
