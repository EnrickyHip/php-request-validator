<?php

declare(strict_types=1);

namespace Enricky\RequestValidator;

use Enricky\RequestValidator\Abstract\AttributeInterface;

/**
 * Internal class to represent an attribute (any key-value in a data array is an attribute)
 * @internal
 */
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
