<?php

namespace Enricky\RequestValidator;

use Enricky\RequestValidator\Abstract\FieldInterface;

class Field implements FieldInterface
{
    private string $name;
    private mixed $value = null;

    public function __construct(array $data, string $name)
    {
        $this->name = $name;

        if (isset($data[$name]) && $data[$name] !== "") {
            $this->value = $data[$name];
        }
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
