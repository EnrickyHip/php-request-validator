<?php

declare(strict_types=1);

namespace Enricky\RequestValidator\Rules;

use Enricky\RequestValidator\Abstract\ValidationRule;

class MaxRule extends ValidationRule
{
    private int $max;
    protected string $message = "field :fieldName length is bigger than :max";

    public function __construct(int $max, ?string $message = null)
    {
        parent::__construct($message);
        $this->max = $max;
        $this->params = [
            ":max" => $this->max,
        ];
    }

    public function validate(mixed $value): bool
    {
        if (!is_string($value)) {
            return false;
        }

        return mb_strlen($value) <= $this->max;
    }
}
