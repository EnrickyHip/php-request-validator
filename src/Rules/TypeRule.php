<?php

declare(strict_types=1);

namespace Enricky\RequestValidator\Rules;

use Enricky\RequestValidator\Abstract\ValidationRule;
use Enricky\RequestValidator\Enums\DataType;
use Enricky\RequestValidator\Enums\InvalidDataTypeException;

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
        $this->type = $type === DataType::FLOAT ? DataType::NUMERIC : $type;
    }

    public function validate(mixed $value): bool
    {
        $function = "is_" . mb_strtolower($this->type->value);

        if (!function_exists($function)) {
            return false;
        }

        return $function($value);
    }

    public function isMajor(): bool
    {
        return true;
    }
}
