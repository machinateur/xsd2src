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

namespace App\Model\Data;

use App\Model\Data\Content\Type;
use RuntimeException;

/**
 * Class Content
 * @package App\Model\Data
 */
class Content
{
    /**
     * @var Content\Type[]
     */
    protected array $typeList;

    /**
     * Content constructor.
     */
    public function __construct()
    {
        $this->typeList = [];
    }

    /**
     * @return Content\Type[]
     */
    public function getTypeList(): array
    {
        return $this->typeList;
    }

    /**
     * @param Content\Type[] $typeList
     * @return Content
     */
    public function setTypeList(array $typeList): Content
    {
        $this->typeList = $typeList;
        return $this;
    }

    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////

    /**
     * @param Content\Type $type
     * @return Content
     */
    public function addType(Content\Type $type): Content
    {
        $this->typeList[$type->getName()] = $type;
        return $this;
    }

    /**
     * @param Content\Type $type
     * @return Content
     */
    public function removeType(Content\Type $type): Content
    {
        unset($this->typeList[$type->getName()]);
        return $this;
    }

    /**
     * @param string $typeName
     * @return Content
     */
    public function removeTypeByName(string $typeName): Content
    {
        unset($this->typeList[$typeName]);
        return $this;
    }

    /**
     * @param string $typeName
     * @return Type
     */
    public function getTypeByName(string $typeName): Type
    {
        if (!$this->hasTypeByName($typeName)) {
            throw new RuntimeException('Error: The type must be present in the type list.');
        }

        return $this->typeList[$typeName];
    }

    /**
     * @param Content\Type $type
     * @return bool
     */
    public function hasType(Content\Type $type): bool
    {
        return isset($this->typeList[$type->getName()]);
    }

    /**
     * @param string $typeName
     * @return bool
     */
    public function hasTypeByName(string $typeName): bool
    {
        return isset($this->typeList[$typeName]);
    }
}
