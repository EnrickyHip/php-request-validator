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
