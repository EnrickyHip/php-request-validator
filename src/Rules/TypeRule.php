<?php

declare(strict_types=1);

namespace Enricky\RequestValidator\Rules;

use Enricky\RequestValidator\Abstract\ValidationRule;
use Enricky\RequestValidator\Enums\DataType;
use Enricky\RequestValidator\Exceptions\InvalidDataTypeException;

class TypeRule extends ValidationRule
{
    private DataType $type;

    public function __construct(DataType|string $type, string $message)
    {
        if (is_string($type)) {
            $type = DataType::tryFrom(strtolower($type));
        }

        if (!$type) {
            throw new InvalidDataTypeException("Value '$type' is not a valid data type.");
        }

        parent::__construct($message);
        $this->type = $type;
    }

    public function validate(mixed $value): bool
    {
        if ($value === null) {
            return true;
        }

        return $this->type->validate($value);
    }

    public function isMajor(): bool
    {
        return true;
    }
}
