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
     * Add a data type validation for a field.
     * This is a facade method to easily add a TypeRule validation.
     * @param DataTypeInterface|string $type expected field type.
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
     */
    public function type(DataTypeInterface|string $type, ?string $message = null, bool $strict = true): self
    {
        $rule = new TypeRule($type, $message, $strict);
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

    /**
     * Add a email rule for a field.
     *
     * @param string|null $message Optional custom error message for the rule.
     *
     * ```php
     * $this->validateField("field")->isEmail("invalid email");
     * ```
     */
    public function isEmail(?string $message = null): self
    {
        $rule = new IsEmailRule($message);
        $this->addRule($rule);
        return $this;
    }

    /**
     * Add a url rule for a field.
     *
     * @param string|null $message Optional custom error message for the rule.
     *
     * ```php
     * $this->validateField("field")->isUrl("invalid url");
     * ```
     */
    public function isUrl(?string $message = null): self
    {
        $rule = new IsUrlRule($message);
        $this->addRule($rule);
        return $this;
    }

    //TODO need better documentation like "Force the field to be an array"
    /**
     * Add an array rule for a field.
     *
     * @param string|null $message Optional custom error message for the rule.
     *
     * ```php
     * $this->validateField("field")->isUrl("invalid url");
     * ```
     */
    public function isArray(?string $message = null): self
    {
        $rule = new IsArrayRule($message);
        $this->addRule($rule);
        return $this;
    }

    /**
     * Add a match rule for a field.
     *
     * @param string $match The regular expression pattern to match against.
     * @param string|null $message The match error message for the rule.
     */
    public function match(string $match, ?string $message = null): self
    {
        $rule = new MatchRule($match, $message);
        $this->addRule($rule);
        return $this;
    }

    /**
     * Add a date string rule for a field.
     *
     * @param string $format The expected format for the date string (default: "Y-m-d").
     * @param string|null $message The custom error message for the rule.
     *
     */
    public function isDateString(string $format = "Y-m-d", ?string $message = null): self
    {
        $rule = new IsDateStringRule($format, $message);
        $this->addRule($rule);
        return $this;
    }

    /**
     * Add a max rule for a field.
     *
     * @param int|float $max The maximum length allowed for the string field.
     * @param string|null $message The custom error message for the rule.
     */
    public function max(int|float $max, ?string $message = null): self
    {
        $rule = new MaxRule($max, $message);
        $this->addRule($rule);
        return $this;
    }

    /**
     * Add a min rule for a field.
     *
     * @param int|float $min The minimum length allowed for the string field.
     * @param string|null $message The custom error message for the rule.
     */
    public function min(int|float $min, ?string $message = null): self
    {
        $rule = new MinRule($min, $message);
        $this->addRule($rule);
        return $this;
    }


    /**
     * Create a new ValidEnumRule instance.
     *
     * @param string $enumClass The class name of the enum.
     * @param string|null $message The custom error message for the rule.
     *
     * @throws InvalidEnumException If the provided string is not a backed enum class.
     */
    public function isEnum(string $enumClass, ?string $message = null): self
    {
        $rule = new ValidEnumRule($enumClass, $message);
        $this->addRule($rule);
        return $this;
    }
}
