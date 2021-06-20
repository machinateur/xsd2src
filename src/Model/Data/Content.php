<?php

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
