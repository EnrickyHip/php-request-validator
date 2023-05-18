<?php

declare(strict_types=1);

namespace Enricky\RequestValidator\Rules;

use BackedEnum;
use Enricky\RequestValidator\Abstract\ValidationRule;
use Enricky\RequestValidator\Exceptions\InvalidEnumException;

class ValidEnumRule extends ValidationRule
{
    private string $enumClass;
    protected string $message = "field :fieldName is not a part of the enum :enum";

    public function __construct(string $enumClass, ?string $message = null)
    {
        if (!is_subclass_of($enumClass, BackedEnum::class)) {
            throw new InvalidEnumException("class is not a Backed Enum!");
        }

        parent::__construct($message);
        $this->enumClass = $enumClass;
    }

    public function validate(mixed $value): bool
    {
        return (bool)$this->enumClass::tryFrom($value);
    }
}
