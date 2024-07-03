<?php

declare(strict_types=1);

namespace Enricky\RequestValidator;

use Enricky\RequestValidator\Abstract\AttributeInterface;
use Enricky\RequestValidator\Types\FileType;
use Enricky\RequestValidator\Rules\FileTypeRule;
use Enricky\RequestValidator\Rules\IsFileRule;
use Enricky\RequestValidator\Rules\MaxFileSizeRule;

/**
 * Builder class that allow you to add validation rules for a file. Do not use this class directly. Use RequestsValidator::validateFile() instead.
 * Add your desired validation rules for a file:
 *
 * Using directly:
 * 
 * ```php
 * 
 * $request = new RequestValidator($data);
 * $request->validateFile("profileImg")
 *     ->type([FileType::PNG, FileType::JPEG], "Invalid file format!")
 *     ->maxSize(5_000_000, "too big!");
 * ```
 * 
 * Using as a class:
 * ```php
 * class MyRequest extends RequestValidator
 * {
 *     public function rules(): array
 *     {
 *         $this->validateFile("profileImg")
 *             ->type(["png", "jpg"], "Invalid file format!")
 *             ->maxSize(5_000_000, "too big!");
 *     }
 * }
 *
 * ```
 * @internal
 */
class FileValidator extends Validator
{
    public function __construct(AttributeInterface $attribute, ?string $message = null)
    {
        parent::__construct($attribute);

        if ($attribute->getValue() !== null) {
            $this->addRule(new IsFileRule($message));
        }
    }

    /**
     * force a file to have an specific mime type (extension).
     * 
     * @param (FileType|string)[]|string|FileType $types allowed types
     * @param ?string $message optional custom message
     * @return FileValidator The instance of FileValidator to allow chaining another validation rules.
     *
     * Call using `FileType` enum:
     *
     * ```php
     * $this->validateFile("profileImg")->type([FileType::PNG, FileType::JPEG]);
     * ```
     *
     * or using extension strings:
     *
     * ```php
     * $this->validateFile("profileImg")->type(["png", "jpg"]);
     * $this->validateFile("profileImg")->type([".png", ".jpg"]);
     * ```
     */
    public function type(array|string|FileType $types, ?string $message = null): self
    {
        $rule = new FileTypeRule($types, $message);
        $this->addRule($rule);
        return $this;
    }

    /**
     * force a file to have a maximum size.
     * 
     * @param int $size max size allowed in bytes.
     * @param ?string $message optional custom message
     * @return FileValidator The instance of FileValidator to allow chaining another validation rules.
     *
     * ```php
     * $this->validateFile("profileImg")->maxSize(5_000_000, "max size of 5MB");
     * ```
     */
    public function maxSize(int $size, ?string $message = null): self
    {
        $rule = new MaxFileSizeRule($size, $message);
        $this->addRule($rule);
        return $this;
    }
}
