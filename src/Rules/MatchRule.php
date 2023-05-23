<?php

declare(strict_types=1);

namespace Enricky\RequestValidator\Rules;

use Enricky\RequestValidator\Abstract\ValidationRule;

/** Rule to validate if a value matches a given regular expression */
class MatchRule extends ValidationRule
{
    private string $match;
    protected string $message = "field :fieldName does not match the given regular expression";

    public function __construct(string $match, ?string $message = null)
    {
        parent::__construct($message);
        $this->match = $match;
    }

    /**
     * Create a new MatchRule instance.
     *
     * @param string $match The regular expression pattern to match against.
     * @param string|null $message The custom error message for the rule.
     */
    public function validate(mixed $value): bool
    {
        return (bool)preg_match($this->match, $value);
    }
}
