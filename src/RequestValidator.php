<?php

declare(strict_types=1);

namespace Enricky\RequestValidator;

use Enricky\RequestValidator\Abstract\ValidatorInterface;
use Enricky\RequestValidator\ArrayValidator;
use Enricky\RequestValidator\Attribute;
use Enricky\RequestValidator\FieldValidator;
use Enricky\RequestValidator\File;
use Enricky\RequestValidator\FileValidator;

/** class to represent a request validation.  */
class RequestValidator
{
    /** @var string[] $errors */
    private array $errors = [];

    /** @var mixed[] $data */
    protected array $data;

    /** @var mixed[] $nullables */
    private array $nullables = ["null", "", "undefined"];

    /** @var ValidatorInterface[] $validators */
    protected array $validators = [];

    /** @param mixed[] $data */
    public function __construct(array &$data)
    {
        $this->data = &$data;
    }

    /**
     * A method to defines validaton rules for the request data.
     */
    public function rules(): void
    {
        // This method is used to allow add rules for a request in a child class
    }

    /** 
     * get the validators of the request validator.
     * 
     * @return ValidatorInterface[] $validators
     */
    public function getValidators(): array
    {
        return $this->validators;
    }

    /**
     * Validates the request data based on the defined rules.
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
     * @return string[] An array of validation error messages.
     */
    final public function getErrors(): array
    {
        $this->rules();

        foreach ($this->validators as $validator) {
            $this->errors = [...$this->errors, ...$validator->getErrors()];
        }

        return array_values(array_unique($this->errors));
    }

    /**
     * Checks if at least one of the given validators were sent.
     * @param string[] $fields An array of fields to check.
     * @param string $message A custom error message.
     * @param bool $exclusive When true it will only be validated if only one field was sent.
     */
    final public function requireOr(array $fields, string $message, bool $exclusive = false): void
    {
        $valid = false;

        foreach ($fields as $field) {
            if (isset($this->data[$field])) {
                if ($valid && $exclusive) {
                    $valid = false;
                    break;
                }

                $valid = true;

                if (!$exclusive) {
                    break;
                }
            }
        }

        if (!$valid) {
            $this->errors[] = $message;
        }
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
        $fieldValidator = new FieldValidator($attriubte);

        $this->validators[] = $fieldValidator;
        return $fieldValidator;
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
    final public function validateFile(string $name, ?string $message = null): FileValidator
    {
        $value = null;

        if (!$this->checkEmpty($name)) {
            $value = new File($this->data[$name]);
        }

        $attriubte = new Attribute($name, $value);
        $fileValidator = new FileValidator($attriubte, $message);
        $this->validators[] = $fileValidator;
        return $fileValidator;
    }

    final public function validateArray(string $name, ?string $message = null): ArrayValidator
    {
        $value = null;

        if (!$this->checkEmpty($name)) {
            $value = $this->data[$name];
        }

        $attriubte = new Attribute($name, $value);
        $arrayValidator = new ArrayValidator($attriubte, $message);
        $this->validators[] = $arrayValidator;
        return $arrayValidator;
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
