<?php

declare(strict_types=1);

namespace Enricky\RequestValidator\Rules;

use Enricky\RequestValidator\Abstract\ValidationRule;

/** Allows defining a group of rules to a field where at least one of them should be valid. */
class ValidateOrRule extends ValidationRule
{
    /** @var ValidationRule[] $rules */
    private array $rules;
    private bool $exclusive;

    /**
     * @param ValidationRule[] $rules rules to validate
     * @param null|string $message optional custom error message for the rule.
     * @param bool $exclusive When true it will only be validated if only one rule was validated.
     */
    public function __construct(array $rules, ?string $message = null, bool $exclusive = false)
    {
        parent::__construct($message);
        $this->rules = $rules;
        $this->exclusive = $exclusive;
    }

    public function validate(mixed $value): bool
    {
        $valid = false;

        foreach ($this->rules as $rule) {
            if ($rule->validate($value)) {
                if ($valid && $this->exclusive) {
                    $valid = false;
                    break;
                }

                $valid = true;

                if (!$this->exclusive) {
                    break;
                }
            }
        }

        return $valid;
    }

    /**
     * Get group of rules
     * @return ValidationRule[] Array of validation rules
     */
    public function getRules(): array
    {
        return $this->rules;
    }

    /**
     * return if the rule is exclusive (only one of the group rules should be valid)
     * @return bool true if rule is exclusive, false otherwise
     */
    public function isExclusive(): bool
    {
        return $this->exclusive;
    }
}
