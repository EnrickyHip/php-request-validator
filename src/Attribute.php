<?php

namespace Enricky\RequestValidator;

use Enricky\RequestValidator\Abstract\AttributeInterface;

class Attribute implements AttributeInterface
{
    private string $name;
    private mixed $value;

    public function __construct(string $name, mixed $value)
    {
        $this->name = $name;
        $this->value = $value;
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
