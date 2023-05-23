<?php

declare(strict_types=1);

namespace Enricky\RequestValidator\Rules;

use Closure;
use Enricky\RequestValidator\Abstract\ValidationRule;

/** Allows defining a custom validation rule to be used once without needing to create separeted class. */
class CustomRule extends ValidationRule
{
    private Closure $condition;

    /**
     * Create a new CustomRule instance.
     *
     * @param Closure(mixed $value): bool $condition A closure containing the validation logic.
     * @param string|null $message Optional custom error message for the rule.
     *
     * ```php
     * $customRule = new CustomRule(fn(mixed $value) => $value === "valid value");
     * $customRule->validate("valid value"); //true;
     * $customRule->validate("invalid value"); //false;
     * ```
     */
    public function __construct(Closure $condition, ?string $message = null)
    {
        parent::__construct($message);
        $this->condition = $condition;
    }

    public function validate(mixed $value): bool
    {
        $closure = $this->condition;
        return $closure($value);
    }
}
