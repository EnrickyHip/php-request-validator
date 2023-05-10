<?php

declare(strict_types=1);

namespace Enricky\RequestValidator\Rules;

use DateTime;
use Enricky\RequestValidator\Abstract\ValidationRule;

class IsDateTimeRule extends ValidationRule
{
    private string $format;

    public function __construct(string $message, string $format = "Y-m-d")
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
