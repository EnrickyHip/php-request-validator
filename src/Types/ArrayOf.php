<?php

namespace Enricky\RequestValidator\Types;

use Enricky\RequestValidator\Abstract\DataTypeInterface;
use Enricky\RequestValidator\Exceptions\InvalidDataTypeException;

class ArrayOf implements DataTypeInterface
{
    private ?DataTypeInterface $type = null;

    public function __construct(DataTypeInterface|string|null $type = null)
    {
        if (is_string($type)) {
            $type = DataType::tryFrom(strtolower($type));
            if (!$type) {
                throw new InvalidDataTypeException("Invalid data type '$type'");
            }
        }

        $this->type = $type;
    }

    public function strictValidate(mixed $value): bool
    {
        if (!is_array($value)) {
            return false;
        }

        if (!$this->type) {
            return true;
        }

        foreach ($value as $element) {
            if (!$this->type->strictValidate($element)) {
                return false;
            }
        }

        return true;
    }

    public function validate(mixed $value): bool
    {
        if (!is_array($value)) {
            return false;
        }

        if (!$this->type) {
            return true;
        }

        foreach ($value as $element) {
            if (!$this->type->validate($element)) {
                return false;
            }
        }

        return true;
    }

    public function getName(): string
    {
        if (!$this->type) {
            return "array";
        }

        return $this->type->getName() . "[]";
    }

    public function getType(): DataTypeInterface
    {
        return $this->type;
    }
}
