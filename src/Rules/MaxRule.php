<?php

declare(strict_types=1);

namespace Enricky\RequestValidator\Rules;

use Enricky\RequestValidator\Abstract\ValidationRule;

/** Rule to validate the maximum length of a string field. */
class MaxRule extends ValidationRule
{
    private int|float $max;
    protected string $message = "field :name length is bigger than :max";

    /**
     * Create a new MaxRule instance.
     *
     * @param int|float $max The maximum length allowed for the string field.
     * @param string|null $message The custom error message for the rule.
     */
    public function __construct(int|float $max, ?string $message = null)
    {
        parent::__construct($message);
        $this->max = $max;
        $this->params = [
            ":max" => $this->max,
        ];
    }

    public function validate(mixed $value): bool
    {
        if (is_array($value)) {
            return count($value) <= $this->max;
        }

        if (is_int($value) || is_float($value)) {
            return $value <= $this->max;
        }

        if (is_string($value)) {
            return mb_strlen($value) <= $this->max;
        }

        return false;
    }

    public function getMax(): int|float
    {
        return $this->max;
    }
}
