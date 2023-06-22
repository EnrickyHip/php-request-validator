<?php

declare(strict_types=1);

namespace Enricky\RequestValidator\Abstract;

use Enricky\RequestValidator\Attribute;
use Enricky\RequestValidator\FieldValidator;
use Enricky\RequestValidator\File;
use Enricky\RequestValidator\FileValidator;

/** Abstract class to represent a request validation.  */
abstract class RequestValidator
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

    /**
     * Creates a field validator. This is a builder class that allows you to add validation rules to a field.
     * @param string $name field key name
     * @return FieldValidator field validator instance
     *
     * Add your desired validation rules for a field:
     *
     * ```php
     *
     * class MyRequest extends RequestValidator
     * {
     *     public function rules(): array
     *     {
     *          $emailValidator = $this->validateField("email")
     *              ->isRequired()
     *              ->type(DataType::STRING)
     *              ->addRule(new IsEmailRule("Invalid Email!"));
     *
     *           return [$emailValidator];
     *     }
     * }
     *
     * ```
     */
    final public function validateField(string $name): FieldValidator
    {
        $value = null;

        if (!$this->checkEmpty($name)) {
            $value = $this->data[$name];
        }

        $attriubte = new Attribute($name, $value);
        return new FieldValidator($attriubte);
    }

    /**
     * Creates a file validator. This is a builder class that allows you to add validation rules to a file field.
     * @param string $name field key name
     * @return FileValidator field validator instance
     *
     * Add your desired validation rules for a file:
     *
     * ```php
     * class MyRequest extends RequestValidator
     * {
     *     public function rules(): array
     *     {
     *         $profileImgValidator = $this->validateFile("profileImg")
     *             ->type([FileType::PNG, FileType::JPEG], "Invalid file format!")
     *             ->maxSize(5_000_000, "too big!");
     *
     *          return [$profileImgValidator];
     *     }
     * }
     * ```
     */
    final public function validateFile(string $name): FileValidator
    {
        $value = null;

        if (!$this->checkEmpty($name)) {
            $value = new File($this->data[$name]);
        }

        $attriubte = new Attribute($name, $value);
        return new FileValidator($attriubte);
    }

    private function checkEmpty(mixed $name): bool
    {
        if (!isset($this->data[$name]) || in_array($this->data[$name], $this->nullables, true)) {
            $this->data[$name] = null;
            return true;
        }

        return false;
    }
}
