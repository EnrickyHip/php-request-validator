<?php

declare(strict_types=1);

namespace Enricky\RequestValidator\Rules;

use Enricky\RequestValidator\Abstract\ValidationRule;

/** Rule to validate the maximum length of an array. */
class MaxLengthRule extends ValidationRule
{
    private int $max;
    protected string $message = "array :name length is bigger than :max";

    /**
     * Create a new MaxLengthRule instance.
     *
     * @param int $max The maximum length allowed for the array.
     * @param string|null $message The custom error message for the rule.
     */
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
        if (!is_array($value)) {
            return false;
        }

        return count($value) <= $this->max;
    }

    public function getMax(): int
    {
        return $this->max;
    }

    public function isMajor(): bool
    {
        return true;
    }
}
