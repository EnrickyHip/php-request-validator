<?php

declare(strict_types=1);

namespace Enricky\RequestValidator\Rules;

use Closure;
use Enricky\RequestValidator\Abstract\ValidationRule;

/** Rule to prohibit a field from being sent. */
class IsProhibitedRule extends ValidationRule
{
    private bool $condition;
    protected string $message = "field :fieldName is prohibited";

    /**
     * Create a new IsProhibitedRule instance.
     *
     * @param bool|Closure(): bool $condition The condition to prohibit the field or not.
     * @param string|null $message The custom error message for the rule.
     */
    public function __construct(bool|Closure $condition, ?string $message = null)
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
