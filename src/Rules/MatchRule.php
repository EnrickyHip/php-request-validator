<?php

declare(strict_types=1);

namespace Enricky\RequestValidator\Rules;

use Enricky\RequestValidator\Abstract\ValidationRule;

class MatchRule extends ValidationRule
{
    private string $match;
    protected string $message = "field :fieldName does not match the given regular expression";

    public function __construct(string $match, ?string $message = null)
    {
        parent::__construct($message);
        $this->match = $match;
    }

    public function validate(mixed $value): bool
    {
        return (bool)preg_match($this->match, $value);
    }
}
