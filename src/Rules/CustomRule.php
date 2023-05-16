<?php

declare(strict_types=1);

namespace Enricky\RequestValidator\Rules;

use Closure;
use Enricky\RequestValidator\Abstract\ValidationRule;

class CustomRule extends ValidationRule
{
    /** @var Closure(mixed $value): bool $condition */
    private Closure $condition;

    /** @param bool|Closure(mixed $value): bool $condition */
    public function __construct(Closure $condition, string $message)
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
