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
    protected array $existingTypeList;

    /**
     * @var string[]
     */
    protected array $usedTypeList;

    /**
     * @var string[]
     */
    protected array $missingTypeList;

    /**
     * TypeMapSimple constructor.
     * @param string[] $typeMap
     * @param string $defaultType
     */
    public function __construct(array $typeMap, string $defaultType = 'string')
    {
        $this->typeMap = $typeMap;
        $this->defaultType = $defaultType;
        $this->existingTypeList = [];
        $this->usedTypeList = [];
        $this->missingTypeList = [];
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
        $this->resetTypeLists();

        $typeList = $content->getTypeList();
        foreach ($typeList as $type) {
            $this->processType($type);
        }

        return $this;
    }

    /**
     * @param Content\Type $type
     */
    public function processType(Content\Type $type): void
    {
        $name = $type->getName();

        $this->registerExistingType($name);

        if ($type->isRoot()) {
            $this->registerUsedType($name);
        }

        $parent = $type->getParent();
        if (null !== $parent && $type->isWithContent()) {
            // Look for simple type...
            $parent = $this->findType($parent, true);

            $type->setParent($parent);

            $this->registerUsedType($parent);
        }

        $elementList = $type->getElementList();
        foreach ($elementList as $element) {
            $this->processElement($element);
        }

        $attributeList = $type->getAttributeList();
        foreach ($attributeList as $attribute) {
            $this->processAttribute($attribute);
        }
    }

    /**
     * @param Content\Type\Element $element
     */
    public function processElement(Content\Type\Element $element): void
    {
        $type = $element->getType();
        $type = $this->findType($type, false);

        $element->setType($type);

        $this->registerUsedType($type);
    }

    /**
     * @param Content\Type\Attribute $attribute
     */
    public function processAttribute(Content\Type\Attribute $attribute): void
    {
        $type = $attribute->getType();
        $type = $this->findType($type, true);

        $attribute->setType($type);

        $this->registerUsedType($type);
    }

    /**
     * @param string $type
     * @param bool $useDefault
     * @return string
     */
    protected function findType(string $type, bool $useDefault = true): string
    {
        if (isset($this->typeMap[$type])) {
            // Avoid circular reference and thereby endless loop.
            $typePath = [];

            while (isset($this->typeMap[$type]) && !in_array($type, $typePath, true)) {
                $typePath[] = $type;
                $type = $this->typeMap[$type];
            }
        } elseif ($useDefault) {
            $this->registerMissingType($type);

            $type = $this->getDefaultType();
        }

        return $type;
    }

    /**
     * @param Content $content
     * @return string[]
     */
    public function getWarningList(Content $content): array
    {
        /** @var string[] $usedTypeList */
        $usedTypeList = $this->usedTypeList;
        /** @var string[] $missingTypeList */
        $missingTypeList = $this->missingTypeList;

        /** @var string[] $existingTypeList */
        $existingTypeList = array_merge(array_keys($this->typeMap), $this->existingTypeList);

        $warningList = [];

        $typeList = $content->getTypeList();
        foreach ($typeList as $type) {
            $name = $type->getName();

            // Find parents that do not exist.
            $parent = $type->getParent();
            if (null !== $parent && !in_array($parent, $existingTypeList, true)) {
                $warningList[] = 'For type "' . $name . '": The parent type "' . $parent . '" is not defined.';
            }

            if (null === $parent && $type->isWithContent()) {
                $warningList[] = 'For type "' . $name . '": The parent type "null" is not allowed for a (simple) type.';
            }


            // Find types that are not used.
            if (!in_array($name, $usedTypeList, true)) {
                $warningList[] = 'For type "' . $name . '": The type is unused.';
            }

            $elementList = $type->getElementList();
            foreach ($elementList as $element) {
                // Find element types that do not exist.
                if (!in_array($element->getType(), $existingTypeList, true)) {
                    if ($type->isWithContent()) {
                        $warningList[] = 'For type "' . $name . '", element "' . $element->getName() . '" with type "' . $element->getType() . '": The type is not mapped.';
                    } else {
                        $warningList[] = 'For type "' . $name . '", element "' . $element->getName() . '" with type "' . $element->getType() . '": The type is not defined.';
                    }
                }
            }

            $attributeList = $type->getAttributeList();
            foreach ($attributeList as $attribute) {
                // Find attribute types that do not exist.
                if (!in_array($attribute->getType(), $existingTypeList, true)) {
                    $warningList[] = 'For type "' . $name . '", attribute "' . $attribute->getName() . '" with type "' . $attribute->getType() . '": The type is not defined.';
                }
            }

            if (0 === count($elementList) && 0 === count($attributeList) && $type->isWithContent()) {
                $warningList[] = 'For type "' . $name . '": The (simple) type is redundant.';
            }
        }

        // Add warnings about the (simple) types that are not mapped.
        foreach ($missingTypeList as $missingType) {
            $warningList[] = 'For type "' . $missingType . '": The (simple) type is not mapped.';
        }

        return $warningList;
    }

    protected
    function resetTypeLists(): void
    {
        $this->missingTypeList = [];
        $this->usedTypeList = [];
        $this->missingTypeList = [];
    }

    /**
     * @param string $type
     */
    protected
    function registerExistingType(string $type): void
    {
        if (!in_array($type, $this->existingTypeList, true)) {
            $this->existingTypeList[] = $type;
        }
    }

    /**
     * @param string $type
     */
    protected
    function registerUsedType(string $type): void
    {
        if (!in_array($type, $this->usedTypeList, true)) {
            $this->usedTypeList[] = $type;
        }
    }

    /**
     * @param string $type
     */
    protected
    function registerMissingType(string $type): void
    {
        if (!in_array($type, $this->usedTypeList, true)) {
            $this->missingTypeList[] = $type;
        }
    }
}
