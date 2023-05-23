<?php

declare(strict_types=1);

namespace Enricky\RequestValidator\Rules;

use Enricky\RequestValidator\Abstract\ValidationRule;

class MinRule extends ValidationRule
{
    private int $min;
    protected string $message = "field :fieldName length is lower than :min";

    public function __construct(int $min, ?string $message = null)
    {
        parent::__construct($message);
        $this->min = $min;
        $this->params = [
            ":min" => $this->min,
        ];
    }

    public function validate(mixed $value): bool
    {
        if (!is_string($value)) {
            return false;
        }

        return mb_strlen($value) >= $this->min;
    }
}
