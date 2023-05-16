<?php

declare(strict_types=1);

namespace Enricky\RequestValidator\Rules;

use Closure;
use Enricky\RequestValidator\Abstract\ValidationRule;

class IsProhibitedRule extends ValidationRule
{
    private bool $condition;

    public function __construct(bool|Closure $condition, string $message)
    {
        if ($condition instanceof Closure) {
            $condition = $condition();
        }

        parent::__construct($message);
        $this->condition = $condition;
    }

    public function validate(mixed $value): bool
    {
        if (!$this->condition) {
            return true;
        }

        return !isset($value);
    }

    public function isMajor(): bool
    {
        return true;
    }
}
