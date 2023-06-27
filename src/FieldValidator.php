<?php

declare(strict_types=1);

namespace Enricky\RequestValidator;

use Closure;
use Enricky\RequestValidator\Enums\DataType;
use Enricky\RequestValidator\Rules\CustomRule;
use Enricky\RequestValidator\Rules\TypeRule;

/**
 * Builder class that allow you to add validation rules for a field. Do not use this class directly. Use RequestsValidator::validateField() instead.
 * Add your desired validation rules for a field:
 *
 * ```php
 * class MyRequest extends RequestValidator
 * {
 *     public function rules(): array
 *     {
 *          $emailValidator = $this->validateField("email")
 *              ->isRequired()
 *              ->type(DataType::STRING)
 *              ->addRule(new IsEmailRule("Invalid Email!"));
 *
 *           return [$emailValidator];
 *     }
 * }
 *
 * ```
 * @internal
 */
class FieldValidator extends Validator
{
    /**
     * Add a data type validation for a field.
     * This is a facade method to easily add a TypeRule validation.
     * @param DataType|string $type expected field type.
     * @param ?string $message optional custom message
     * @return FieldValidator The instance of FieldValidator to allow chaining another validation rules.
     *
     * Call using `DataType` enum:
     *
     * ```php
     * $this->validateField("age")->type(DataType::INT);
     * ```
     *
     * or using strings:
     *
     * ```php
     * $this->validateField("age")->type("int");
     * ```
     */
    public function type(DataType|string $type, ?string $message = null): self
    {
        $rule = new TypeRule($type, $message);
        $this->addRule($rule);
        return $this;
    }

    /**
     * Add a custom rule for a field.
     *
     * @param Closure(mixed $value): bool $condition A closure containing the validation logic.
     * @param string|null $message Optional custom error message for the rule.
     *
     * ```php
     * $condition = fn(mixed $value) => $value === "valid value";
     * $this->validateField("field")->custom($condition, "invalid field");
     * ```
     */
    public function custom(Closure $condition, ?string $message = null): self
    {
        $rule = new CustomRule($condition, $message);
        $this->addRule($rule);
        return $this;
    }
}
