<?php

declare(strict_types=1);

namespace Enricky\RequestValidator\Rules;

use DateTime;
use Enricky\RequestValidator\Abstract\ValidationRule;

class IsDateTimeRule extends ValidationRule
{
    private string $format;
    protected string $message = "field :fieldName is not a valid date";

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
