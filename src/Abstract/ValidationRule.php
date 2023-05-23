<?php

declare(strict_types=1);

namespace Enricky\RequestValidator\Abstract;

/** Representation of a validation rule. */
abstract class ValidationRule
{
    /** @var string $message Default error message for the validation rule. */
    protected string $message = "field :fieldName is invalid";

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
     * @internal
     * Resolve the error message by replacing placeholders with actual values.
     * The two placeholders `:fieldName` and `:fieldValue` are built in and will be replaced automatically.
     *
     * @param FieldInterface $field The field being validated.
     * @return string The resolved error message.
     *
     * If you want to create your own placeholders, set the property `$this->params` inside your constructor and add the placeholders to the property `$this->message`:
     *
     * ```php
     * class YourRule extends ValidationRule
     * {
     *      protected string $message = "Field :fieldName is invalid with parameters :param1 and :param2";
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
     */
    final public function resolveMessage(FieldInterface $field): string
    {
        $stringifiedParams = array_map(function ($value) {
            return $this->stringifyParam($value);
        }, $this->params);

        $params = [
            ...$stringifiedParams,
            ":fieldName" => $this->stringifyParam($field->getName()),
            ":fieldValue" => $this->stringifyParam($field->getValue()),
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
        };
    }

    private function replaceParams(array $params): string
    {
        $message = $this->message;

        foreach ($params as $param => $value) {
            $message = str_replace($param, $value, $message);
        }

        return $message;
    }
}
