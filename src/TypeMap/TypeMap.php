<?php

namespace App\TypeMap;

use App\Model\Data\Content;

/**
 * Class TypeMap
 * @package App\TypeMap
 */
class TypeMap implements TypeMapInterface
{
    /**
     * @var string[]
     */
    protected array $typeMap;

    protected string $defaultType;

    /**
     * @var string[]
     */
    protected array $typeList;

    /**
     * TypeMapSimple constructor.
     * @param string[] $typeMap
     * @param string $defaultType
     */
    public function __construct(array $typeMap, string $defaultType = 'string')
    {
        $this->typeMap = $typeMap;
        $this->defaultType = $defaultType;
        $this->typeList = [];
    }

    /**
     * @return string[]
     */
    public function getTypeMap(): array
    {
        return $this->typeMap;
    }

    /**
     * @return string
     */
    public function getDefaultType(): string
    {
        return $this->defaultType;
    }

    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////

    /**
     * @inheritDoc
     */
    public function entry(string $typeFrom, string $typeTo): TypeMapInterface
    {
        $this->typeMap[$typeFrom] = $typeTo;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function process(Content $content): TypeMapInterface
    {
        $this->typeList = [];

        $typeMap = array_keys($this->typeMap);
        foreach ($typeMap as $type) {
            $this->typeList[] = $type;
        }

        $typeList = $content->getTypeList();
        foreach ($typeList as $type) {
            $this->processType($type);
        }

        return $this;
    }

    /**
     * @param Content\Type $type
     * @return TypeMapInterface
     */
    public function processType(Content\Type $type): TypeMapInterface
    {
        $parent = $type->getParent();
        if (null !== $parent && $type->isWithContent()) {
            $parent = $this->findType($parent);

            $type->setParent($parent);
        }

        $this->typeList[] = $type->getName();

        $elementList = $type->getElementList();
        foreach ($elementList as $element) {
            $this->processElement($element);
        }

        $attributeList = $type->getAttributeList();
        foreach ($attributeList as $attribute) {
            $this->processAttribute($attribute);
        }

        return $this;
    }

    /**
     * @param Content\Type\Element $element
     * @return TypeMapInterface
     */
    public function processElement(Content\Type\Element $element): TypeMapInterface
    {
        $type = $element->getType();
        $type = $this->findType($type, false);

        $element->setType($type);

        return $this;
    }

    /**
     * @param Content\Type\Attribute $attribute
     * @return TypeMapInterface
     */
    public function processAttribute(Content\Type\Attribute $attribute): TypeMapInterface
    {
        $type = $attribute->getType();
        $type = $this->findType($type, true);

        $attribute->setType($type);

        return $this;
    }

    /**
     * @param string $type
     * @param bool $useDefault
     * @return string
     */
    protected function findType(string $type, bool $useDefault = true): string
    {
        // Always add to type list.
        $this->typeList[] = $type;

        if (isset($this->typeMap[$type])) {
            // Avoid circular reference and thereby endless loop.
            $typePath = [];

            while (isset($this->typeMap[$type]) && !in_array($type, $typePath, true)) {
                $typePath[] = $type;
                $type = $this->typeMap[$type];
            }
        } elseif ($useDefault) {
            $type = $this->getDefaultType();
        }

        return $type;
    }

    /**
     * @param string $type
     * @param bool $useDefault
     * @return bool
     */
    protected function checkType(string $type, bool $useDefault = true): bool
    {
        return $type !== $this->findType($type, $useDefault);
    }

    /**
     * @return string[]
     */
    public function getWarnMessageList(Content $content): array
    {
        $warnMessageList = [];

        $typeList = $content->getTypeList();
        foreach ($typeList as $type) {
            // Find parents that are not in types.
            $parent = $type->getParent();
            if (null !== $parent && !in_array($parent, $this->typeList, true)) {
                $warnMessageList[] = 'For type "' . $type->getName() . '": The parent "' . $parent . '" is not defined.';
            }

            // Find types that are not used (?).
            if (!in_array($type->getName(), $this->typeList, true) && (!$type->isRoot() || !$type->isWithContent())) {
                $warnMessageList[] = 'For type "' . $type->getName() . '": The type seems to be unused.';
            }

            $elementList = $type->getElementList();
            foreach ($elementList as $element) {
                // Find element types that are not in types.
                if (!in_array($element->getType(), $this->typeList, true)) {
                    $warnMessageList[] = 'For type "' . $type->getName() . '", element "' . $element->getType() . '": The type is not defined.';
                }
            }

            $attributeList = $type->getAttributeList();
            foreach ($attributeList as $attribute) {
                // Find attribute types that are not in types.
                if (!in_array($attribute->getType(), $this->typeList, true)) {
                    $warnMessageList[] = 'For type "' . $type->getName() . '", attribute "' . $attribute->getType() . '": The type is not defined.';
                }
            }
        }

        return $warnMessageList;
    }
}
