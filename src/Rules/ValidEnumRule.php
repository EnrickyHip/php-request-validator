<?php

declare(strict_types=1);

namespace Enricky\RequestValidator\Rules;

use Enricky\RequestValidator\Abstract\ValidationRule;
use Enricky\RequestValidator\Exceptions\InvalidEnumException;
use UnitEnum;

class ValidEnumRule extends ValidationRule
{
    private string $enumClass;

    public function __construct(string $enumClass, string $message)
    {
        if (!is_subclass_of($enumClass, UnitEnum::class)) {
            throw new InvalidEnumException("class is not a Enum!");
        }

        parent::__construct($message);
        $this->enumClass = $enumClass;
    }

    public function validate(mixed $value): bool
    {
        return (bool)$this->enumClass::tryFrom($value);
    }
}
