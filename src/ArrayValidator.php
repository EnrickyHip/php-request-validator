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
     * @param DataTypeInterface|string $type expected elements type.
     * @param ?string $message optional custom message
     * @param bool $strict set strict type validation
     * @return ArrayValidator The instance of ArrayValidator to allow chaining another validation rules.
     *
     * Call using `DataType` enum:
     *
     * ```php
     * $this->validateArray("age")->type(DataType::INT);
     * ```
     *
     * or using strings:
     *
     * ```php
     * $this->validateArray("age")->type("int");
     * ```
     */
    public function type(DataTypeInterface|string $type, ?string $message = null, bool $strict = true): self
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
    public function maxLength(int $max, ?string $message = null): self
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
    public function minLength(int $min, ?string $message = null): self
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

        //TODO talvez um array vazio retorne false quando requerido?? usar isRequired ou criar regra nova
        $value = $this->attribute->getValue();

        if ($value === null || empty($value)) {
            return true;
        }

        foreach ($this->rules as $rule) {
            foreach ($this->attribute->getValue() as $element) {
                if (!$rule->validate($element)) {
                    $this->errors[] = $rule->resolveMessage($this->attribute);

                }
            }
        }

        $this->isValid = empty($this->errors);
        return $this->isValid;
    }
}
