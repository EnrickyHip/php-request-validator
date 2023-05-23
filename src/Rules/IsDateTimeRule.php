<?php

declare(strict_types=1);

namespace Enricky\RequestValidator\Rules;

use DateTime;
use Enricky\RequestValidator\Abstract\ValidationRule;

/** Rule to validate if a value is a valid date time string.*/
class IsDateTimeRule extends ValidationRule
{
    private string $format;
    protected string $message = "field :fieldName is not a valid date";

    /**
     * Create a new IsDateTimeRule instance.
     *
     * @param string $format The expected format for the date string (default: "Y-m-d").
     * @param string|null $message The custom error message for the rule.
     *
     * ```php
     * $rule = new IsDateTimeRule();
     * $rule->validate("2023-05-23"); //true
     * $rule->validate("23/05/2023"); //false
     *
     * $rule = new IsDateTimeRule("d/m/Y");
     * $rule->validate("2023-05-23"); //false
     * $rule->validate("23/05/2023"); //true
     * ```
     */
    public function __construct(string $format = "Y-m-d", ?string $message = null)
    {
        parent::__construct($message);
        $this->format = $format;
    }

    public function validate(mixed $value): bool
    {
        $date = DateTime::createFromFormat($this->format, $value);
        return $date instanceof DateTime;
    }
}
