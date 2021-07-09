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

namespace App\Model\Data\Content;

use RuntimeException;

/**
 * Class Type
 * @package App\Model\Data\Content
 */
class Type
{
    private string $name;

    private bool $root;

    private ?string $parent;

    private bool $withContent;

    /**
     * @var Type\Attribute[]
     */
    private array $attributeList;
    /**
     * @var Type\Element[]
     */
    private array $elementList;

    /**
     * Type constructor.
     * @param string $name
     * @param bool $root
     */
    public function __construct(string $name, bool $root = false)
    {
        $this->name = $name;
        $this->root = $root;
        $this->parent = null;
        $this->withContent = false;
        $this->attributeList = [];
        $this->elementList = [];
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return Type
     */
    public function setName(string $name): Type
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return bool
     */
    public function isRoot(): bool
    {
        return $this->root;
    }

    /**
     * @param bool $root
     * @return Type
     */
    public function setRoot(bool $root): Type
    {
        $this->root = $root;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getParent(): ?string
    {
        return $this->parent;
    }

    /**
     * @param string|null $parent
     * @return Type
     */
    public function setParent(?string $parent): Type
    {
        $this->parent = $parent;
        return $this;
    }

    /**
     * @return bool
     */
    public function isWithContent(): bool
    {
        return $this->withContent;
    }

    /**
     * @param bool $withContent
     * @return Type
     */
    public function setWithContent(bool $withContent): Type
    {
        $this->withContent = $withContent;
        return $this;
    }

    /**
     * @return Type\Attribute[]
     */
    public function getAttributeList(): array
    {
        return $this->attributeList;
    }

    /**
     * @param Type\Attribute[] $attributeList
     * @return Type
     */
    public function setAttributeList(array $attributeList): Type
    {
        $this->attributeList = $attributeList;
        return $this;
    }

    /**
     * @return Type\Element[]
     */
    public function getElementList(): array
    {
        return $this->elementList;
    }

    /**
     * @param Type\Element[] $elementList
     * @return Type
     */
    public function setElementList(array $elementList): Type
    {
        $this->elementList = $elementList;
        return $this;
    }

    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////

    /**
     * @param Type\Attribute $attribute
     * @return Type
     */
    public function addAttribute(Type\Attribute $attribute): Type
    {
        $this->attributeList[$attribute->getName()] = $attribute;
        return $this;
    }

    /**
     * @param Type\Attribute $attribute
     * @return Type
     */
    public function removeAttribute(Type\Attribute $attribute): Type
    {
        unset($this->attributeList[$attribute->getName()]);
        return $this;
    }

    /**
     * @param string $attributeName
     * @return Type
     */
    public function removeAttributeByName(string $attributeName): Type
    {
        unset($this->attributeList[$attributeName]);
        return $this;
    }

    /**
     * @param string $attributeName
     * @return Type\Attribute
     */
    public function getAttributeByName(string $attributeName): Type\Attribute
    {
        if (!$this->hasAttributeByName($attributeName)) {
            throw new RuntimeException('Error: The attribute must be present in the attribute list.');
        }

        return $this->attributeList[$attributeName];
    }

    /**
     * @param Type\Attribute $attribute
     * @return bool
     */
    public function hasAttribute(Type\Attribute $attribute): bool
    {
        return isset($this->attributeList[$attribute->getName()]);
    }

    /**
     * @param string $attributeName
     * @return bool
     */
    public function hasAttributeByName(string $attributeName): bool
    {
        return isset($this->attributeList[$attributeName]);
    }

    /**
     * @param Type\Element $element
     * @return Type
     */
    public function addElement(Type\Element $element): Type
    {
        $this->elementList[$element->getName()] = $element;
        return $this;
    }

    /**
     * @param Type\Element $element
     * @return Type
     */
    public function removeElement(Type\Element $element): Type
    {
        unset($this->elementList[$element->getName()]);
        return $this;
    }

    /**
     * @param string $elementName
     * @return Type
     */
    public function removeElementByName(string $elementName): Type
    {
        unset($this->elementList[$elementName]);
        return $this;
    }

    /**
     * @param string $elementName
     * @return Type\Element
     */
    public function getElementByName(string $elementName): Type\Element
    {
        if (!$this->hasElementByName($elementName)) {
            throw new RuntimeException('Error: The element must be present in the element list.');
        }

        return $this->elementList[$elementName];
    }

    /**
     * @param Type\Element $element
     * @return bool
     */
    public function hasElement(Type\Element $element): bool
    {
        return isset($this->elementList[$element->getName()]);
    }

    /**
     * @param string $elementName
     * @return bool
     */
    public function hasElementByName(string $elementName): bool
    {
        return isset($this->elementList[$elementName]);
    }
}
