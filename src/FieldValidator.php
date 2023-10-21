<?php

declare(strict_types=1);

namespace Enricky\RequestValidator;

use Closure;
use Enricky\RequestValidator\Abstract\DataTypeInterface;
use Enricky\RequestValidator\Exceptions\InvalidEnumException;
use Enricky\RequestValidator\Rules\CustomRule;
use Enricky\RequestValidator\Rules\IsArrayRule;
use Enricky\RequestValidator\Rules\IsDateStringRule;
use Enricky\RequestValidator\Rules\IsEmailRule;
use Enricky\RequestValidator\Rules\IsUrlRule;
use Enricky\RequestValidator\Rules\MatchRule;
use Enricky\RequestValidator\Rules\MaxRule;
use Enricky\RequestValidator\Rules\MinRule;
use Enricky\RequestValidator\Rules\NotEmptyRule;
use Enricky\RequestValidator\Rules\TypeRule;
use Enricky\RequestValidator\Rules\ValidEnumRule;

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
    public function custom(Closure $condition, ?string $message = null): static
    {
        $rule = new CustomRule($condition, $message);
        $this->addRule($rule);
        return $this;
    }

    /**
     * force a field to be an email.
     *
     * @param string|null $message Optional custom error message for the rule.
     *
     * ```php
     * $this->validateField("field")->isEmail("invalid email");
     * ```
     */
    public function isEmail(?string $message = null): static
    {
        $rule = new IsEmailRule($message);
        $this->addRule($rule);
        return $this;
    }

    /**
     * force a field to be an url.
     *
     * @param string|null $message Optional custom error message for the rule.
     *
     * ```php
     * $this->validateField("field")->isUrl("invalid url");
     * ```
     */
    public function isUrl(?string $message = null): static
    {
        $rule = new IsUrlRule($message);
        $this->addRule($rule);
        return $this;
    }

    /**
     * force a field to be an array.
     *
     * @param string|null $message Optional custom error message for the rule.
     *
     * ```php
     * $this->validateField("field")->isArray("not an array");
     * ```
     *
     * consider using `RequestValidator::validateArray()` to a better array validation.
     */
    public function isArray(?string $message = null): static
    {
        $rule = new IsArrayRule($message);
        $this->addRule($rule);
        return $this;
    }
    /**
     * force a field to not be empty
     *
     * @param string|null $message Optional custom error message for the rule.
     *
     * ```php
     * $this->validateField("field")->notEmpty("is empty");
     * ```
     *
     */
    public function notEmpty(?string $message = null): static
    {
        $rule = new NotEmptyRule($message);
        $this->addRule($rule);
        return $this;
    }

    /**
     * force a field to match a given pattern.
     *
     * @param string $match The regular expression pattern to match against.
     * @param string|null $message The match error message for the rule.
     */
    public function match(string $match, ?string $message = null): static
    {
        $rule = new MatchRule($match, $message);
        $this->addRule($rule);
        return $this;
    }

    /**
     * force a field to be a valid date string.
     *
     * @param string $format The expected format for the date string (default: "Y-m-d").
     * @param string|null $message The custom error message for the rule.
     *
     */
    public function isDateString(string $format = "Y-m-d", ?string $message = null): static
    {
        $rule = new IsDateStringRule($format, $message);
        $this->addRule($rule);
        return $this;
    }

    /**
     * force a field to have a maximum length or value.
     * If given a string this rule will force the string to have a maximum length.
     * If given a number this rule will force the number to have maximum value.
     * If given an array this rule will force the array to have maximum length.
     *
     * @param int|float $max The maximum length allowed for the string field.
     * @param string|null $message The custom error message for the rule.
     */
    public function max(int|float $max, ?string $message = null): static
    {
        $rule = new MaxRule($max, $message);
        $this->addRule($rule);
        return $this;
    }

    /**
     * force a field to have a maximum length or value.
     * If given a string this rule will force the string to have a maximum length.
     * If given a number this rule will force the number to have maximum value.
     * If given an array this rule will force the array to have maximum length.
     *
     * @param int|float $min The minimum length allowed for the string field.
     * @param string|null $message The custom error message for the rule.
     */
    public function min(int|float $min, ?string $message = null): static
    {
        $rule = new MinRule($min, $message);
        $this->addRule($rule);
        return $this;
    }


    /**
     * force a field to be a valid enum value of a given PHP enum class.
     *
     * @param string $enumClass The class name of the enum.
     * @param string|null $message The custom error message for the rule.
     *
     * @throws InvalidEnumException If the provided string is not a backed enum class.
     */
    public function isEnum(string $enumClass, ?string $message = null): static
    {
        $rule = new ValidEnumRule($enumClass, $message);
        $this->addRule($rule);
        return $this;
    }
}
