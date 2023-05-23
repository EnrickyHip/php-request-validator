<?php

declare(strict_types=1);

namespace Enricky\RequestValidator\Rules;

use Closure;
use Enricky\RequestValidator\Abstract\ValidationRule;

/** Rule to require a field. */
class IsRequiredRule extends ValidationRule
{
    private bool $condition;
    protected string $message = "field :fieldName is required";

    /**
     * Create a new IsProhibitedRule instance.
     *
     * @param bool|Closure(): bool $condition The condition to require the field or not.
     * @param string|null $message The custom error message for the rule.
     */
    public function __construct(?string $message = null, bool|Closure $condition = true)
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
