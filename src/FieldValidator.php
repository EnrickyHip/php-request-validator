<?php

declare(strict_types=1);

namespace Enricky\RequestValidator;

use Closure;
use Enricky\RequestValidator\Abstract\ValidationRule;
use Enricky\RequestValidator\Enums\DataType;
use Enricky\RequestValidator\Rules\IsProhibitedRule;
use Enricky\RequestValidator\Rules\IsRequiredRule;
use Enricky\RequestValidator\Rules\TypeRule;

class FieldValidator
{
    /** @var ValidationRule[] $majorRules */
    private array $majorRules = [];

    /** @var ValidationRule[] $rules */
    private array $rules = [];

    /** @var string[] $errors */
    private ?array $errors = null;

    private ?bool $isValid = null;
    private Field $field;

    public function __construct(Field $field)
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
        $rule = new IsProhibitedRule($condition, $msg ?? "field '{$this->field}' cannot be send");
        $this->addRule($rule);
        return $this;
    }

    public function type(DataType|string $type, ?string $msg = null): self
    {
        $typeName = $type instanceof DataType ? $type->value : $type;
        $rule = new TypeRule($type, $msg ?? "field '{$this->field}' is not of the type '$typeName");
        $this->addRule($rule);
        return $this;
    }

    /** @param ValidationRule[] $dependencies */
    public function addRule(ValidationRule $rule): self
    {
        if ($rule->isMajor()) {
            $this->majorRules[] = $rule;
        } else {
            $this->rules[] = $rule;
        }

        return $this;
    }

    public function getField(): Field
    {
        return $this->field;
    }


    public function isValid(): bool
    {
        if ($this->isValid === null) {
            $this->isValid = empty($this->getErrors());
        }

        return $this->isValid;
    }

    /** @return string[] */
    public function getErrors(): array
    {
        if ($this->errors !== null) {
            return $this->errors;
        }

        $this->errors = [];

        foreach ($this->majorRules as $rule) {
            if (!$rule->validate($this->field->getValue())) {
                $this->isValid = false;
                $this->errors = [$rule->getMessage()];
                return $this->errors;
            }
        }

        if ($this->field->getValue() !== null) {
            foreach ($this->rules as $rule) {
                if (!$rule->validate($this->field->getValue())) {
                    $this->errors[] = $rule->getMessage();
                }
            }
        }

        $this->isValid = empty($this->errors);
        return $this->errors;
    }
}
