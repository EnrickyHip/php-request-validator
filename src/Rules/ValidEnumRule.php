<?php

declare(strict_types=1);

namespace Enricky\RequestValidator\Rules;

use BackedEnum;
use Enricky\RequestValidator\Abstract\ValidateRuleException;
use Enricky\RequestValidator\Abstract\ValidationRule;
use UnexpectedValueException;

class ValidEnumRule extends ValidationRule
{
    private string $enumClass;

    public function __construct(string $enumClass, string $message)
    {
        if (!is_subclass_of($enumClass, BackedEnum::class)) {
            throw new ValidateRuleException("class is not a Enum!");
        }

        parent::__construct($message);
        $this->enumClass = $enumClass;
    }

    public function validate(mixed $value): bool
    {
        try {
            $this->enumClass::from($value);
            return true;
        } catch (UnexpectedValueException $exception) {
            return false;
        }
    }
}
