<?php

declare(strict_types=1);

namespace Enricky\RequestValidator;

use Enricky\RequestValidator\Abstract\AttributeInterface;
use Enricky\RequestValidator\Enums\FileType;
use Enricky\RequestValidator\Rules\FileTypeRule;
use Enricky\RequestValidator\Rules\IsFileRule;
use Enricky\RequestValidator\Rules\MaxFileSizeRule;

class FileValidator extends Validator
{
    public function __construct(AttributeInterface $attribute, ?string $message = null)
    {
        parent::__construct($attribute);
        $this->rules[] = new IsFileRule($message);
    }

    /** @param (FileType|string)[]|string|FileType $types */
    public function type(array|string|FileType $types, ?string $message = null): self
    {
        $rule = new FileTypeRule($types, $message);
        $this->addRule($rule);
        return $this;
    }

    public function maxSize(int $size, ?string $message = null): self
    {
        $rule = new MaxFileSizeRule($size, $message);
        $this->addRule($rule);
        return $this;
    }
}
