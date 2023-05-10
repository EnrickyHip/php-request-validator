<?php

declare(strict_types=1);

namespace Enricky\RequestValidator\Rules;

use Enricky\RequestValidator\Abstract\ValidationRule;

class MinRule extends ValidationRule
{
    private int $min;

    public function __construct(int $min, string $message)
    {
        parent::__construct($message);
        $this->min = $min;
    }

    public function validate(mixed $value): bool
    {
        if (!is_string($value)) {
            return false;
        }

        return mb_strlen($value) >= $this->min;
    }
}
