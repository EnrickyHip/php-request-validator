<?php

declare(strict_types=1);

namespace Enricky\RequestValidator\Rules;

use Enricky\Enums\DataType;
use Enricky\RequestValidator\Abstract\ValidationRule;

class TypeRule extends ValidationRule
{
    private DataType $type;

    public function __construct(DataType $type, string $message)
    {
        parent::__construct($message);
        $this->type = $type === DataType::FLOAT ? DataType::NUMERIC : $type;
    }

    public function validate(mixed $value): bool
    {
        $function = "is_" . mb_strtolower($this->type->value);

        if (!function_exists($function)) {
            return false;
        }

        return $function($value);
    }

    public function isMajor(): bool
    {
        return true;
    }
}
