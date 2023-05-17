<?php

declare(strict_types=1);

namespace Enricky\RequestValidator\Enums;

enum DataType: string
{
    case STRING = "string";
    case INT = "int";
    case FLOAT = "float";
    case BOOL = "bool";

    public function validate(mixed $value): bool
    {
        return match ($this) {
            self::STRING => is_string($value),
            self::INT => is_int($value),
            self::FLOAT => is_float($value) || is_int($value),
            self::BOOL => is_bool($value),
        };
    }
}
