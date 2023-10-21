<?php

declare(strict_types=1);

namespace Enricky\RequestValidator;

use Enricky\RequestValidator\Abstract\DataTypeInterface;
use Enricky\RequestValidator\Rules\TypeRule;
use Enricky\RequestValidator\Traits\FieldRuleFactories;

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
    use FieldRuleFactories;

    /**
     * force a field to have an specific data type.
     * @param DataTypeInterface|string|(string|DataTypeInterface)[] $types expected field types.
     * @param ?string $message optional custom message
     * @param bool $strict set strict type validation
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
     * checking against multiple types (validate if the value is a string or an integer):
     *
     * ```php
     * $this->validateField("age")->type(["int", DataType::STRING]);
     * ```
     */
    public function type(DataTypeInterface|string|array $types, ?string $message = null, bool $strict = true): static
    {
        $rule = new TypeRule($types, $message, $strict);
        $this->addRule($rule);
        return $this;
    }
}
