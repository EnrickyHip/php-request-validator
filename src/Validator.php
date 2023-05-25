<?php

declare(strict_types=1);

namespace Enricky\RequestValidator;

use Closure;
use Enricky\RequestValidator\Abstract\AttributeInterface;
use Enricky\RequestValidator\Abstract\ValidationRule;
use Enricky\RequestValidator\Abstract\ValidatorInterface;
use Enricky\RequestValidator\Rules\IsProhibitedRule;
use Enricky\RequestValidator\Rules\IsRequiredRule;

abstract class Validator implements ValidatorInterface
{
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

    public function isRequired(?string $msg = null): static
    {
        $rule = new IsRequiredRule($msg);
        $this->addRule($rule);
        return $this;
    }

    /** @param bool|Closure(): bool $condition  */
    public function isRequiredIf(bool|Closure $condition, ?string $msg = null): static
    {
        $rule = new IsRequiredRule($msg, $condition);
        $this->addRule($rule);
        return $this;
    }

    /** @param bool|Closure(): bool $condition  */
    public function prohibitedIf(bool|Closure $condition, ?string $msg = null): static
    {
        $rule = new IsProhibitedRule($condition, $msg);
        $this->addRule($rule);
        return $this;
    }

    public function addRule(ValidationRule $rule): static
    {
        if ($rule->isMajor()) {
            $this->majorRules[] = $rule;
        } else {
            $this->rules[] = $rule;
        }

        return $this;
    }

    /** @return ValidationRule[] */
    public function getRules(): array
    {
        return [...$this->rules, ...$this->majorRules];
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
