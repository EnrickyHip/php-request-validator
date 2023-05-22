<?php

declare(strict_types=1);

namespace Enricky\RequestValidator\Abstract;

abstract class ValidationRule
{
  protected string $message = "field :fieldName is invalid";
  protected array $params = [];

  public function __construct(?string $customMessage = null)
  {
    if ($customMessage !== null) {
      $this->message = $customMessage;
    }
  }

  abstract public function validate(mixed $value): bool;

  /** This method returns if a rule is a major rule. To be a major rule means it has validation
   *  priority over simple validations. To make a rule major override this method in your extended rule and set return to `true`; */
  public function isMajor(): bool
  {
    return false;
  }

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
