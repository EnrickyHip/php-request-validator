<?php

declare(strict_types=1);

namespace Enricky\RequestValidator\Enums;


/** Enum representation of the data types that can be handled by the validators. */
enum DataType: string
{
    /** DataType case representing a string. */
    case STRING = "string";

    /** DataType case representing an integer. */
    case INT = "int";

    /** DataType case representing a floating number. */
    case FLOAT = "float";

    /** DataType case representing a boolean. */
    case BOOL = "bool";

    /**
     * Strict validate the given value matches the data type.
     *
     * @param mixed $value The value to validate.
     * @return bool True if the value matches the data type, false otherwise.
     *
     * ```php
     * DataType::INT->validate(1); //true
     * DataType::INT->validate("text"); //false
     * ```
     */
    public function strictValidate(mixed $value): bool
    {
        return match ($this) {
            self::STRING => is_string($value),
            self::INT => is_int($value),
            self::FLOAT => is_float($value) || is_int($value),
            self::BOOL => is_bool($value),
        };
    }

    /**
     * Validate the given value matches the data type.
     *
     * @param mixed $value The value to validate.
     * @return bool True if the value matches the data type, false otherwise.
     *
     * ```php
     * DataType::INT->validate(1); //true
     * DataType::INT->validate("text"); //false
     * ```
     */
    public function validate(mixed $value): bool
    {
        $isIntString = function (mixed $value): bool {
            if (!is_string($value)) {
                return false;
            }

            if ($value[0] === '-') {
                return ctype_digit(substr($value, 1));
            }

            return ctype_digit($value);
        };

        return match ($this) {
            self::STRING => is_string($value),
            self::INT => is_int($value) || $isIntString($value),
            self::FLOAT => is_numeric($value),
            self::BOOL => is_bool($value) || in_array($value, ["true", "false", 1, 0, "1", "0"], true),
        };
    }
}
