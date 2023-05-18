<?php

declare(strict_types=1);

namespace Enricky\RequestValidator\Rules;

use Enricky\RequestValidator\Abstract\ValidationRule;

class IsEmailRule extends ValidationRule
{
    protected string $message = "field :fieldName is not a valid email address";

    public function validate(mixed $value): bool
    {
        return (bool)filter_var($value, FILTER_VALIDATE_EMAIL);
    }
}
