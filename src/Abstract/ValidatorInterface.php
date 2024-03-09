<?php

declare(strict_types=1);

namespace Enricky\RequestValidator\Abstract;

interface ValidatorInterface
{
    /**
     * Validate and get all attribute errors.
     * @return string[] array of error messages.
     */
    public function getErrors(): array;

    /** @return bool True if the all the validator's rules are valid, false otherwise. */
    public function validate(): bool;

    /** @return string Get Attribute name */
    public function getName(): string;

    /** @return mixed Get Attribute value */
    public function getValue(): mixed;

    /**
     * Add rule to the attribute.
     * @param ValidationRule $rule
     * @return static Validator instance to allow chaining another validation rules.
     */
    public function addRule(ValidationRule $rule): static;
}
