<?php

declare(strict_types=1);

namespace Enricky\RequestValidator;

use Closure;
use Enricky\RequestValidator\Abstract\AttributeInterface;
use Enricky\RequestValidator\Abstract\ValidationRule;
use Enricky\RequestValidator\Abstract\ValidatorInterface;
use Enricky\RequestValidator\Rules\IsProhibitedRule;
use Enricky\RequestValidator\Rules\IsRequiredRule;
use Enricky\RequestValidator\Rules\ValidateOrRule;

/**
 * Internal abstract class for validators. These is a builder class that allow you to add validation rules to attributes.
 * @internal
 */
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

    /**
     * Require a field.
     * @param ?string $message optional custom message
     * @return static Validator insntance to allow chaining another validation rules.
     */
    public function isRequired(?string $message = null): static
    {
        $rule = new IsRequiredRule($message);
        $this->addRule($rule);
        return $this;
    }

    /**
     * Require a field if a condition is true.
     * @param bool|Closure(): bool $condition
     * @param ?string $message optional custom message
     * @return static Validator insntance to allow chaining another validation rules.
     */
    public function isRequiredIf(bool|Closure $condition, ?string $message = null): static
    {
        $rule = new IsRequiredRule($message, $condition);
        $this->addRule($rule);
        return $this;
    }

    /**
     * Prohibit a field if a condition is true.
     * @param bool|Closure(): bool $condition
     * @param ?string $message optional custom message
     * @return static Validator insntance to allow chaining another validation rules.
     */
    /** @param bool|Closure(): bool $condition  */
    public function prohibitedIf(bool|Closure $condition, ?string $message = null): static
    {
        $rule = new IsProhibitedRule($condition, $message);
        $this->addRule($rule);
        return $this;
    }

    /**
     * Define a group of rules to a field where at least one of them should be valid.
     * @param ValidationRule[] $rules rules to validate
     * @param null|string $message optional custom error message for the rule.
     * @param bool $exclusive When true it will only be validated if only one rule was validated.
     */
    public function validateOr(array $rules, ?string $message = null, bool $exclusive = false): static
    {
        $rule = new ValidateOrRule($rules, $message, $exclusive);
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

    /**
     * Get all rules added to the attribute.
     * @return ValidationRule[] array of rules.
     */
    public function getRules(): array
    {
        return [...$this->majorRules, ...$this->rules,];
    }

    public function getName(): string
    {
        return $this->attribute->getName();
    }

    public function getValue(): mixed
    {
        return $this->attribute->getValue();
    }

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
