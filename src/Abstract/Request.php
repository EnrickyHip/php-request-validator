<?php

declare(strict_types=1);

namespace Enricky\RequestValidator\Abstract;

/** Abstract class to represent a request validation.  */
abstract class Request
{
    /**
     * Defines the validaton rules for the request data.
     *
     * @param array $data The request data to be validated.
     *
     * @return ValidatorInterface[] An array of validator instances.
     *
     * ```php
     * class YourRequest extends Request
     *    {
     *        public function rules(array $data): array
     *        {
     *            $nameValidator = (new FieldValidator($data, "name"))
     *                ->isRequired()
     *                ->type(DataType::STRING)
     *                ->addRule(new MaxRule(150, "too long name!"));
     *
     *            $activeValidator = (new FieldValidator($data, "email"))
     *                ->isRequired()
     *               ->type(DataType::STRING)
     *                ->addRule(new IsEmailRule(":fieldValue is not a valid email"));
     *            return [$nameValidator, $emailValidator];
     *        }
     *    }
     * ```
     */
    abstract public function rules(array $data): array;

    /**
     * Validates the request data based on the defined rules.
     *
     * @param array $data The request data to be validated.
     *
     * @return bool Returns true if the request data is valid, false otherwise.
     */
    final public function validate(array $data): bool
    {
        return empty($this->getErrors($data));
    }

    /**
     * Retrieves all validation errors from the request data based on the defined rules.
     *
     * @param array $data The request data to be validated.
     *
     * @return string[] An array of validation error messages.
     */
    final public function getErrors(array $data): array
    {
        $validators = $this->rules($data);
        $errors = [];

        foreach ($validators as $validator) {
            $errors = [...$errors, ...$validator->getErrors()];
        }

        return array_unique($errors);
    }
}
