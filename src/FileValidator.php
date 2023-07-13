<?php

declare(strict_types=1);

namespace Enricky\RequestValidator;

use Enricky\RequestValidator\Abstract\AttributeInterface;
use Enricky\RequestValidator\Enums\FileType;
use Enricky\RequestValidator\Rules\FileTypeRule;
use Enricky\RequestValidator\Rules\IsFileRule;
use Enricky\RequestValidator\Rules\MaxFileSizeRule;

/**
 * Builder class that allow you to add validation rules for a file. Do not use this class directly. Use RequestsValidator::validateFile() instead.
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
     * Add a type validation for a file.
     * This is a facade method to easily add a FileTypeRule validation.
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
     * Add a max size validation for a file.
     * This is a facade method to easily add a MaxFileSizeRule validation.
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
