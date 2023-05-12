<?php

declare(strict_types=1);

namespace Enricky\RequestValidator\Rules;

use Enricky\RequestValidator\Abstract\ValidationRule;

class IsUrlRule extends ValidationRule
{
    public function validate(mixed $value): bool
    {
        return (bool)filter_var($value, FILTER_VALIDATE_URL);
    }
}