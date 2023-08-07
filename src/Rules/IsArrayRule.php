<?php

declare(strict_types=1);

namespace Enricky\RequestValidator\Rules;

use Enricky\RequestValidator\Abstract\ValidationRule;

/** Rule to validate if a value is an array. */
class IsArrayRule extends ValidationRule
{
    protected string $message = "field :name is not an array";

    public function validate(mixed $value): bool
    {
        return is_array($value);
    }

    public function isMajor(): bool
    {
        return true;
    }
}
