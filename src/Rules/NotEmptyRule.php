<?php

declare(strict_types=1);

namespace Enricky\RequestValidator\Rules;

use Enricky\RequestValidator\Abstract\ValidationRule;

/** Rule to validate if a value is not empty. */
class NotEmptyRule extends ValidationRule
{
    protected string $message = "field :name cannot be empty";
    
    public function validate(mixed $value): bool
    {
        return !empty($value);
    }
    
    public function isMajor(): bool
    {
        return true;
    }   
}