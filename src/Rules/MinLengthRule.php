<?php

declare(strict_types=1);

namespace Enricky\RequestValidator\Rules;

use Enricky\RequestValidator\Abstract\ValidationRule;

/** Rule to validate the minimum length of an array. */
class MinLengthRule extends ValidationRule
{
    private int $min;
    protected string $message = "array :name length is lower than :min";

    /**
     * Create a new MinLengthRule instance.
     *
     * @param int $min The minimum length allowed for the array.
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
        //TODO talvez alterar a mensagem nesses casos e invalidar seria interessante.
        if (!is_array($value)) {
            return false;
        }

        return count($value) >= $this->min;
    }

    public function getMin(): int
    {
        return $this->min;
    }

    public function isMajor(): bool
    {
        return true;
    }
}
