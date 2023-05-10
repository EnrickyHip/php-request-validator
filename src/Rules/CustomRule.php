<?php

declare(strict_types=1);

namespace Enricky\RequestValidator\Rules;

use Closure;
use Enricky\RequestValidator\Abstract\ValidationRule;

class CustomRule extends ValidationRule
{
    /** @var bool|Closure(mixed $value): bool $condition */
    private bool|Closure $condition;

    /** @param bool|Closure(mixed $value): bool $condition */
    public function __construct(bool|Closure $condition, string $message)
    {
        parent::__construct($message);
        $this->condition = $condition;
    }

    public function validate(mixed $value): bool
    {
        if ($this->condition instanceof Closure) {
            $closure = $this->condition;
            $condition = $closure($value);
        } else {
            $condition = $this->condition;
        }

        return $condition;
    }
}
