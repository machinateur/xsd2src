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
