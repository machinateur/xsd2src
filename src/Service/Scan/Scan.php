<?php

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
