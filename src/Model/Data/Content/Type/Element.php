<?php

namespace App\Model\Data\Content\Type;

/**
 * Class Element
 * @package App\Model\Data\Content\Type
 * @see https://www.w3schools.com/xml/el_element.asp
 */
class Element
{
    private string $name;

    private string $type;

    private bool $required;

    private bool $singular;

    private array $payload;

    /**
     * Element constructor.
     * @param string $name
     * @param string $type
     * @param bool $required
     * @param bool $singular
     */
    public function __construct(string $name, string $type, bool $required = false, bool $singular = true)
    {
        $this->name = $name;
        $this->type = $type;
        $this->required = $required;
        $this->singular = $singular;
        $this->payload = [];
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
     * @return Element
     */
    public function setName(string $name): Element
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @param string $type
     * @return Element
     */
    public function setType(string $type): Element
    {
        $this->type = $type;
        return $this;
    }

    /**
     * @return bool
     */
    public function isRequired(): bool
    {
        return $this->required;
    }

    /**
     * @param bool $required
     * @return Element
     */
    public function setRequired(bool $required): Element
    {
        $this->required = $required;
        return $this;
    }

    /**
     * @return bool
     */
    public function isSingular(): bool
    {
        return $this->singular;
    }

    /**
     * @param bool $singular
     * @return Element
     */
    public function setSingular(bool $singular): Element
    {
        $this->singular = $singular;
        return $this;
    }

    /**
     * @return array
     */
    public function getPayload(): array
    {
        return $this->payload;
    }

    /**
     * @param array $payload
     * @return Element
     */
    public function setPayload(array $payload): Element
    {
        $this->payload = $payload;
        return $this;
    }

    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////
}
