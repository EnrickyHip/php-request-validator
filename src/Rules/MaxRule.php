<?php

declare(strict_types=1);

namespace Enricky\RequestValidator\Rules;

use Enricky\RequestValidator\Abstract\ValidationRule;

/** Rule to validate the maximum length of a string field. */
class MaxRule extends ValidationRule
{
    private int $max;
    protected string $message = "field :fieldName length is bigger than :max";

    /**
     * Create a new MaxRule instance.
     *
     * @param int $max The maximum length allowed for the string field.
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
        if (!is_string($value)) {
            return false;
        }

        return mb_strlen($value) <= $this->max;
    }
}
