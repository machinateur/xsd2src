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

use App\Model\Data\Content;
use App\NodeHandle\NodeHandleChain;
use DOMDocument;
use DOMElement;
use LogicException;

/**
 * Class Scan
 * @package App\Service\Scan
 */
final class Scan implements ScanInterface
{
    /** @var int */
    public const SEL_ANY /********/ = 0b0000;
    /** @var int */
    public const SEL_USED /*******/ = 0b0001;
    /** @var int */
    public const SEL_REQUIRED /***/ = 0b0010;
    /** @var int */
    public const SEL_PATTERN /****/ = 0b0100;

    private NodeHandleChain $nodeHandleChain;

    private int $selectMode;

    private ?string $selectPattern;

    /**
     * Scan constructor.
     * @param NodeHandleChain $nodeHandleChain
     */
    public function __construct(NodeHandleChain $nodeHandleChain)
    {
        $this->nodeHandleChain = $nodeHandleChain;
        $this->selectMode = self::SEL_ANY;
        $this->selectPattern = null;
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
     * @param string|null $selectPattern
     * @return Scan
     */
    public function setSelectMode(int $selectMode, ?string $selectPattern = null): Scan
    {
        $this->selectMode = $selectMode;

        if ($selectMode & self::SEL_PATTERN) {
            if (null === $selectPattern) {
                throw new LogicException('The select pattern must be set when passing the flag "SEL_PATTERN".');
            }

            return $this->setSelectPattern($selectPattern);
        }

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
     * @return Scan
     */
    private function setSelectPattern(?string $selectPattern): Scan
    {
        $this->selectPattern = $selectPattern;
        return $this;
    }

    /**
     * @return bool
     */
    public function hasSelectPattern(): bool
    {
        return null !== $this->selectPattern;
    }

    /**
     * @inheritDoc
     */
    public function run(Content $content, DOMDocument $document, string $documentElementName = 'schema'): Content
    {
        /** @var DOMElement $element */
        $element = $document->documentElement;

        if ($documentElementName !== $element->localName) {
            throw new LogicException('The document element must be named "' . $documentElementName . '".');
        }

        $this->nodeHandleChain->handle($element, $content);

        // TODO: Do select!

        return $content;
    }
}
