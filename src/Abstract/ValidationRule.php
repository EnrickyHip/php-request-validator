<?php

declare(strict_types=1);

namespace Enricky\RequestValidator\Abstract;

use Enricky\RequestValidator\Exceptions\InvalidDataTypeException;

/** Representation of a validation rule. */
abstract class ValidationRule
{
    /** @var string $message Default error message for the validation rule. */
    protected string $message = "field :name is invalid";

    /** @var mixed[] $params Array of params to be replaced in the error message. */
    protected array $params = [];

    /** @param string|null $customMessage Custom error message to overwrite the default one. */
    public function __construct(?string $customMessage = null)
    {
        if ($customMessage !== null) {
            $this->message = $customMessage;
        }
    }

    /**
     * Validate the given value against the validation rule.
     *
     * @param mixed $value The value to validate.
     * @return bool True if the value passes the validation rule, false otherwise.
     *
     *  Implement your own validation rule in your extended class:
     *
     * ```php
     * class YourRule extends ValidationRule
     * {
     *      public function validate(mixed $value): bool
     *      {
     *          return $value === "valid";
     *      }
     * }
     * ```
     */
    abstract public function validate(mixed $value): bool;

    /**
     * Check if the validation rule is a major rule.
     *
     * Major rules have higher validation priority over simple rules.
     * If a major rule fails, none of the normal rules are going to be validated.
     * Override this method in your extended rule and set it to `true`
     * if you want to consider it a major rule.
     *
     * @return bool True if the rule is a major rule, false otherwise.
     */
    public function isMajor(): bool
    {
        return false;
    }

    /**
     * Resolve the error message by replacing placeholders with a string representation of the value.
     * The two placeholders `:name` and `:value` are built in and will be replaced automatically.
     *
     * @param AttributeInterface $attribute The attribute being validated.
     * @return string The resolved error message.
     *
     * If you want to create your own placeholders, set the property `$this->params` inside your constructor and add the placeholders to the property `$this->message`:
     *
     * ```php
     * class YourRule extends ValidationRule
     * {
     *      protected string $message = "Attribute :name is invalid with parameters :param1 and :param2";
     *      private string $param1;
     *      private string $param2;
     *
     *      public function __construct(string $param1, int $param2)
     *      {
     *          $this->param1 = $param1;
     *          $this->param2 = $param2;
     *
     *          $this->params = [
     *              ":param1" => $this->param1,
     *              ":param2" => $this->param2,
     *          ];
     *      }
     * }
     * ```
     *
     * This way, the placeholders `:param1` and `:param2` will be replaced by the respective values.
     * The use of colons in the beggining is not mandatory, but recommended.
     *
     * @internal
     */
    final public function resolveMessage(AttributeInterface $attribute): string
    {
        $params = [
            ...$this->stringifyCustomParams(),
            ":name" => $this->stringifyParam($attribute->getName()),
            ":value" => $this->stringifyParam($attribute->getValue()),
        ];

        return $this->replaceParams($params);
    }

    /**
     * Resolve the error message by replacing placeholders with string a representation of the value in a specific element of an array.
     * The two placeholders `:name` and `:value` are built in and will be replaced automatically.
     *
     * @param AttributeInterface $attribute The attribute being validated.
     * @param int|string $index The array index of the element to be replaced by :name.
     * @throws InvalidDataTypeException If the attribute value is not an array.
     * @return string The resolved error message.
     *
     * If you want to create your own placeholders, set the property `$this->params` inside your constructor and add the placeholders to the property `$this->message`:
     *
     * ```php
     * class YourRule extends ValidationRule
     * {
     *      protected string $message = "Attribute :name is invalid with parameters :param1 and :param2";
     *      private string $param1;
     *      private string $param2;
     *
     *      public function __construct(string $param1, int $param2)
     *      {
     *          $this->param1 = $param1;
     *          $this->param2 = $param2;
     *
     *          $this->params = [
     *              ":param1" => $this->param1,
     *              ":param2" => $this->param2,
     *          ];
     *      }
     * }
     * ```
     *
     * This way, the placeholders `:param1` and `:param2` will be replaced by the respective values.
     * The use of colons in the beggining is not mandatory, but recommended.
     *
     * @internal
     */
    final public function resolveArrayMessage(AttributeInterface $attribute, int|string $index): string
    {
        if (!is_array($attribute->getValue())) {
            throw new InvalidDataTypeException("Attribute value should be an array to the message be resolved.");
        }
        
        $params = [
            ...$this->stringifyCustomParams(),
            ":name" => $this->stringifyParam($attribute->getName()),
            ":value" => $this->stringifyParam($attribute->getValue()[$index]),
        ];

        return $this->replaceParams($params);
    }

    /**
     * Get the error message for the validation rule.
     *
     * @return string The error message.
     */
    final public function getMessage(): string
    {
        return $this->message;
    }

    /** @return string[] */
    private function stringifyCustomParams(): array
    {
        return array_map(function (mixed $value) {
            return $this->stringifyParam($value);
        }, $this->params);
    }

    private function stringifyParam(mixed $value): string
    {
        return match (gettype($value)) {
            "string" => "'$value'",
            "boolean" => $value ? "true" : "false",
            "integer" => (string)$value,
            "double" => (string)$value,
            "array" => "[array]",
            "object" => "{object}",
            "NULL" => "null",
            "unknown type" => "unknown",
            "resource", "resource (closed)" => "{resource}",
        };
    }

    /** @param string[] $params */
    private function replaceParams(array $params): string
    {
        $message = $this->message;

        foreach ($params as $param => $value) {
            $message = str_replace($param, $value, $message);
        }

        return $message;
    }
}
