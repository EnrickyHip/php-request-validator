<?php

namespace Enricky\RequestValidator\Abstract;

interface ValidatorInterface
{
    /** @return string[] The validator's error messages. */
    public function getErrors(): array;

    /** @return bool True if the all the validator's rules are valid, false otherwise. */
    public function validate(): bool;

    public function addRule(ValidationRule $rule): static;
}
