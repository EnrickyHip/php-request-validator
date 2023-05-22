<?php

declare(strict_types=1);

namespace Enricky\RequestValidator\Rules;

use Closure;
use Enricky\RequestValidator\Abstract\ValidationRule;

class IsRequiredRule extends ValidationRule
{
    private bool $condition;
    protected string $message = "field :fieldName is required'";

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
