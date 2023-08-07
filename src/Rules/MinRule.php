<?php

declare(strict_types=1);

namespace Enricky\RequestValidator\Rules;

use Enricky\RequestValidator\Abstract\ValidationRule;

/** Rule to validate the minimum length of a string field. */
class MinRule extends ValidationRule
{
    private int|float $min;
    protected string $message = "field :name length is lower than :min";

    /**
     * Create a new MaxRule instance.
     *
     * @param int|float $min The minimum length allowed for the string field.
     * @param string|null $message The custom error message for the rule.
     */
    public function __construct(int|float $min, ?string $message = null)
    {
        parent::__construct($message);
        $this->min = $min;
        $this->params = [
            ":min" => $this->min,
        ];
    }

    public function validate(mixed $value): bool
    {
        if (is_array($value)) {
            return count($value) >= $this->min;
        }

        if (is_int($value) || is_float($value)) {
            return $value >= $this->min;
        }

        if (is_string($value)) {
            return mb_strlen($value) >= $this->min;
        }

        return false;
    }


    public function getMin(): int|float
    {
        return $this->min;
    }
}
