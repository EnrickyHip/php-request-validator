<?php

use Enricky\RequestValidator\Abstract\AttributeInterface;

class AttributeMock implements AttributeInterface
{
    public function __construct(
        private string $name = "name",
        private mixed $value = "value"
    ) {
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getValue(): mixed
    {
        return $this->value;
    }
}
