<?php

declare(strict_types=1);

namespace Enricky\RequestValidator\Rules;

use Enricky\RequestValidator\Abstract\ValidationRule;

class MatchRule extends ValidationRule
{
    private string $match;

    public function __construct(string $match, string $message)
    {
        parent::__construct($message);
        $this->match = $match;
    }

    public function validate(mixed $value): bool
    {
        return preg_match($this->match, $value);
    }
}
