<?php

declare(strict_types=1);

namespace Enricky\RequestValidator;

use Closure;
use Enricky\RequestValidator\Abstract\FieldInterface;
use Enricky\RequestValidator\Abstract\ValidationRule;
use Enricky\RequestValidator\Abstract\ValidatorInterface;
use Enricky\RequestValidator\Enums\DataType;
use Enricky\RequestValidator\Rules\IsProhibitedRule;
use Enricky\RequestValidator\Rules\IsRequiredRule;
use Enricky\RequestValidator\Rules\TypeRule;

class FieldValidator implements ValidatorInterface
{
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

    public function isRequired(?string $msg = null): self
    {
        $rule = new IsRequiredRule($msg);
        $this->addRule($rule);
        return $this;
    }

    /** @param bool|Closure(): bool $condition  */
    public function isRequiredIf(bool|Closure $condition, ?string $msg = null): self
    {
        $rule = new IsRequiredRule($msg, $condition);
        $this->addRule($rule);
        return $this;
    }

    /** @param bool|Closure(): bool $condition  */
    public function prohibitedIf(bool|Closure $condition, ?string $msg = null): self
    {
        $rule = new IsProhibitedRule($condition, $msg);
        $this->addRule($rule);
        return $this;
    }

    public function type(DataType|string $type, ?string $msg = null): self
    {
        $rule = new TypeRule($type, $msg);
        $this->addRule($rule);
        return $this;
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
