<?php

declare(strict_types=1);

namespace Enricky\RequestValidator\Rules;

use Closure;
use Enricky\RequestValidator\Abstract\ValidationRule;

class IsRequiredRule extends ValidationRule
{
    private bool $condition;

    public function __construct(string $message, bool|Closure $condition = true)
    {
        if ($condition instanceof Closure) {
            $condition =  $condition();
        }

        parent::__construct($message);
        $this->condition = $condition;
    }

    public function validate(mixed $value): bool
    {
        return !$this->condition || isset($value);
    }

    public function isMajor(): bool
    {
        return true;
    }
}
