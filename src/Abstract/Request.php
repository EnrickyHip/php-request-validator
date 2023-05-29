<?php

declare(strict_types=1);

namespace Enricky\RequestValidator\Abstract;

use Enricky\RequestValidator\Attribute;
use Enricky\RequestValidator\FieldValidator;
use Enricky\RequestValidator\File;
use Enricky\RequestValidator\FileValidator;

/** Abstract class to represent a request validation.  */
abstract class Request
{
    protected array $data;
    private $nullables = ["null", "", "undefined"];

    public function __construct(array &$data)
    {
        $this->data = &$data;
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

    final public function validateField(string $name)
    {
        $value = null;

        if (!$this->checkEmpty($name)) {
            $value = $this->data[$name];
        }

        $attriubte = new Attribute($name, $value);
        return new FieldValidator($attriubte);
    }

    final public function validateFile(string $name)
    {
        $value = null;

        if (!$this->checkEmpty($name) && is_array($this->data[$name])) {
            $value = new File($this->data[$name]);
        }

        $attriubte = new Attribute($name, $value);
        return new FileValidator($attriubte);
    }

    private function checkEmpty(mixed $name)
    {
        $isNotSent = !isset($this->data[$name]);
        $isNullable = in_array($this->data[$name], $this->nullables, true);

        if ($isNotSent || $isNullable) {
            $this->data[$name] = null;
            return true;
        }

        return false;
    }
}
