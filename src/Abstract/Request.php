<?php

declare(strict_types=1);

namespace Enricky\RequestValidator\Abstract;

/** Abstract class to represent a request validation.  */
abstract class Request
{
    protected array $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    /**
     * Defines the validaton rules for the request data.
     *
     * @param array $data The request data to be validated.
     *
     * @return ValidatorInterface[] An array of validator instances.
     *
     */
    abstract public function rules(): array;

    /**
     * Validates the request data based on the defined rules.
     *
     * @param array $data The request data to be validated.
     *
     * @return bool Returns true if the request data is valid, false otherwise.
     */
    final public function validate(): bool
    {
        return empty($this->getErrors());
    }

    /**
     * Retrieves all validation errors from the request data based on the defined rules.
     *
     * @param array $data The request data to be validated.
     *
     * @return string[] An array of validation error messages.
     */
    final public function getErrors(): array
    {
        $validators = $this->rules();
        $errors = [];

        foreach ($validators as $validator) {
            $errors = [...$errors, ...$validator->getErrors()];
        }

        return array_unique($errors);
    }
}
