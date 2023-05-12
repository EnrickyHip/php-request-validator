<?php

declare(strict_types=1);

namespace Enricky\RequestValidator\Abstract;

abstract class ValidationRule
{
  protected string $message;

  public function __construct(string $message)
  {
    $this->message = $message;
  }

  abstract public function validate(mixed $value): bool;

  /** This method returns if a rule is a major rule. To be a major rule means it has validation
   *  priority over simple validations. To make a rule major override this method in your extended rule and set return to `true`; */
  public function isMajor(): bool
  {
    return false;
  }

  final public function getMessage(): string
  {
    return $this->message;
  }
}