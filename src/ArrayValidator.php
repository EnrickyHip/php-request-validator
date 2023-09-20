<?php

declare(strict_types=1);

namespace Enricky\RequestValidator;

use Enricky\RequestValidator\Abstract\AttributeInterface;
use Enricky\RequestValidator\Abstract\DataTypeInterface;
use Enricky\RequestValidator\Rules\IsArrayRule;
use Enricky\RequestValidator\Rules\MaxLengthRule;
use Enricky\RequestValidator\Rules\MinLengthRule;
use Enricky\RequestValidator\Rules\TypeRule;
use Enricky\RequestValidator\Types\ArrayOf;

/**
 * Builder class that allow you to add validation rules for an array. Do not use this class directly. Use RequestsValidator::validateArray() instead.
 * Add your desired validation rules for an array:
 *
 * ```php
 * class MyRequest extends RequestValidator
 * {
 *     public function rules(): array
 *     {
 *          $emailArrayValidator = $this->validateArray("emails")
 *              ->isRequired()
 *              ->type(DataType::STRING)
 *              ->addRule(new IsEmailRule("Invalid Email!"));
 *
 *           return [$emailArrayValidator];
 *     }
 * }
 *
 * ```
 * @internal
 */
class ArrayValidator extends FieldValidator
{
    public function __construct(AttributeInterface $attribute, ?string $message = null)
    {
        parent::__construct($attribute);

        if ($attribute->getValue() !== null) {
            $this->addRule(new IsArrayRule($message));
        }
    }

    /**
     * force all elements in a array to be of an specfific data type.
     * @param DataTypeInterface|string|(DataTypeInterface|string)[] $types expected elements types.
     * @param ?string $message optional custom message
     * @param bool $strict set strict type validation
     * @return ArrayValidator The instance of ArrayValidator to allow chaining another validation rules.
     *
     * Call using `DataType` enum:
     *
     * ```php
     * $this->validateArray("ages")->type(DataType::INT);
     * ```
     *
     * or using strings:
     *
     * ```php
     * $this->validateArray("ages")->type("int");
     * ```
     *
     * checking against multiple types (validate if the value is a string or an integer):
     *
     * ```php
     * $this->validateArray("ages")->type(["int", DataType::STRING]);
     * ```
     */
    public function type(DataTypeInterface|string|array $type, ?string $message = null, bool $strict = true): static
    {
        $rule = new TypeRule(new ArrayOf($type), $message, $strict);
        $this->addRule($rule);
        return $this;
    }

    /**
     * force an array to have a maximum length.
     *
     * @param int|float $min The maximum length allowed for the array.
     * @param string|null $message The custom error message for the rule.
     */
    public function maxLength(int $max, ?string $message = null): static
    {
        $rule = new MaxLengthRule($max, $message);
        $this->addRule($rule);
        return $this;
    }

    /**
     * force an array to have a minimum length.
     *
     * @param int|float $min The minimum length allowed for the array.
     * @param string|null $message The custom error message for the rule.
     */
    public function minLength(int $min, ?string $message = null): static
    {
        $rule = new MinLengthRule($min, $message);
        $this->addRule($rule);
        return $this;
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

        $value = $this->attribute->getValue();

        if ($value === null || empty($value)) {
            return true;
        }

        foreach ($this->rules as $rule) {
            foreach ($this->attribute->getValue() as $index => $element) {
                if (!$rule->validate($element)) {
                    $this->errors[] = $rule->resolveArrayMessage($this->attribute, $index);
                }
            }
        }

        $this->isValid = empty($this->errors);
        return $this->isValid;
    }
}
