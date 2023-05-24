<?php

declare(strict_types=1);

namespace Enricky\RequestValidator;

use Enricky\RequestValidator\Abstract\FieldInterface;
use Enricky\RequestValidator\Abstract\ValidationRule;
use Enricky\RequestValidator\Abstract\ValidatorInterface;
use Enricky\RequestValidator\Traits\RequiredRulesSimplifier;
use Enricky\RequestValidator\Traits\TypeRuleSimplifier;

class FieldValidator implements ValidatorInterface
{
    use RequiredRulesSimplifier;
    use TypeRuleSimplifier;

    /** @var ValidationRule[] $majorRules */
    private array $majorRules = [];

    /** @var ValidationRule[] $rules */
    private array $rules = [];

    /** @var string[] $errors */
    private array $errors = [];

    private ?bool $isValid = null;
    private FieldInterface $field;

    public function __construct(FieldInterface $field)
    {
        $this->field = $field;
    }

    public function addRule(ValidationRule $rule): self
    {
        if ($rule->isMajor()) {
            $this->majorRules[] = $rule;
        } else {
            $this->rules[] = $rule;
        }

        return $this;
    }

    public function getField(): FieldInterface
    {
        return $this->field;
    }

    /** @return string[] */
    public function getErrors(): array
    {
        if ($this->isValid === null) {
            $this->validate();
        }

        return $this->errors;
    }

    public function validate(): bool
    {
        $this->isValid = true;
        $this->errors = [];

        foreach ($this->majorRules as $majorRule) {
            if (!$majorRule->validate($this->field->getValue())) {
                $this->errors[] = $majorRule->resolveMessage($this->field);
                $this->isValid = false;
                return false;
            }
        }

        if ($this->field->getValue() === null) {
            return true;
        }

        foreach ($this->rules as $rule) {
            if (!$rule->validate($this->field->getValue())) {
                $this->errors[] = $rule->resolveMessage($this->field);
            }
        }

        $this->isValid = empty($this->errors);
        return $this->isValid;
    }
}
