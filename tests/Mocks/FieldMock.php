<?php

use Enricky\RequestValidator\Abstract\FieldInterface;

class FieldMock implements FieldInterface
{
    public function __construct(
        private string $name,
        private mixed $value
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
