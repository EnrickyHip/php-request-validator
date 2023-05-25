<?php

declare(strict_types=1);

namespace Enricky\RequestValidator;

use Enricky\RequestValidator\Abstract\AttributeInterface;
use Enricky\RequestValidator\Abstract\ValidationRule;
use Enricky\RequestValidator\Abstract\ValidatorInterface;
use Enricky\RequestValidator\Traits\RequiredRulesSimplifier;

abstract class Validator implements ValidatorInterface
{
    use RequiredRulesSimplifier;

    /** @var ValidationRule[] $majorRules */
    protected array $majorRules = [];

    /** @var ValidationRule[] $rules */
    protected array $rules = [];

    /** @var string[] $errors */
    protected array $errors = [];

    protected ?bool $isValid = null;
    protected AttributeInterface $attribute;

    public function __construct(AttributeInterface $attribute)
    {
        $this->attribute = $attribute;
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

    public function getAttribute(): AttributeInterface
    {
        return $this->attribute;
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
            if (!$majorRule->validate($this->attribute->getValue())) {
                $this->errors[] = $majorRule->resolveMessage($this->attribute);
                $this->isValid = false;
                return false;
            }
        }

        if ($this->attribute->getValue() === null) {
            return true;
        }

        foreach ($this->rules as $rule) {
            if (!$rule->validate($this->attribute->getValue())) {
                $this->errors[] = $rule->resolveMessage($this->attribute);
            }
        }

        $this->isValid = empty($this->errors);
        return $this->isValid;
    }
}
