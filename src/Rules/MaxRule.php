<?php

declare(strict_types=1);

namespace Enricky\RequestValidator\Rules;

use Enricky\RequestValidator\Abstract\ValidationRule;

class MaxRule extends ValidationRule
{
    private int $max;

    public function __construct(int $max, string $message)
    {
        parent::__construct($message);
        $this->max = $max;
    }

    public function validate(mixed $value): bool
    {
        if (!is_string($value)) {
            return false;
        }

        return mb_strlen($value) <= $this->max;
    }
}
