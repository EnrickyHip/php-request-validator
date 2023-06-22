<?php

declare(strict_types=1);

namespace Enricky\RequestValidator\Rules;

use Enricky\RequestValidator\Abstract\ValidationRule;

/** Rule to validate the minimum length of a string field. */
class MinRule extends ValidationRule
{
    private int $min;
    protected string $message = "field :attributeName length is lower than :min";

    /**
     * Create a new MaxRule instance.
     *
     * @param int $min The minimum length allowed for the string field.
     * @param string|null $message The custom error message for the rule.
     */
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
        if (is_int($value) || is_float($value)) {
            return $value >= $this->min;
        }

        if (is_string($value)) {
            return mb_strlen($value) >= $this->min;
        }

        return false;
    }
}
