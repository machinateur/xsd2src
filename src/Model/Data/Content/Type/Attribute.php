<?php

namespace App\Model\Data\Content\Type;

/**
 * Class Attribute
 * @package App\Model\Data\Content\Type
 */
class Attribute
{
    private string $name;

    private string $type;

    private bool $required;

    private array $payload;

    /**
     * Attribute constructor.
     * @param string $name
     * @param string $type
     * @param bool $required
     */
    public function __construct(string $name, string $type, bool $required = false)
    {
        $this->name = $name;
        $this->type = $type;
        $this->required = $required;
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
     * @return Attribute
     */
    public function setName(string $name): Attribute
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
     * @return Attribute
     */
    public function setType(string $type): Attribute
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
     * @return Attribute
     */
    public function setRequired(bool $required): Attribute
    {
        $this->required = $required;
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
     * @return Attribute
     */
    public function setPayload(array $payload): Attribute
    {
        $this->payload = $payload;
        return $this;
    }

    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////
}
