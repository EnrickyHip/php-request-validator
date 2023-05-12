<?php

declare(strict_types=1);

namespace Enricky\RequestValidator\Rules;

use Enricky\RequestValidator\Abstract\ValidationRule;
use Exception;
use UnitEnum;

class NotValidEnumException extends Exception
{
}

class ValidEnumRule extends ValidationRule
{
    private string $enumClass;

    public function __construct(string $enumClass, string $message)
    {
        if (!is_subclass_of($enumClass, UnitEnum::class)) {
            throw new NotValidEnumException("class is not a Enum!");
        }

        parent::__construct($message);
        $this->enumClass = $enumClass;
    }

    public function validate(mixed $value): bool
    {
        return (bool)$this->enumClass::tryFrom($value);
    }
}
