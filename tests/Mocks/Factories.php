<?php

use Enricky\RequestValidator\Abstract\DataTypeInterface;
use Enricky\RequestValidator\Abstract\ValidationRule;

function createRule(bool|Closure $valid, bool $isMajor = false): ValidationRule
{
    return new class($valid, $isMajor) extends ValidationRule
    {
        public function __construct(
            private bool|Closure $valid,
            private bool $isMajor,
        ) {
        }

        public function validate(mixed $value): bool
        {
            if ($this->valid instanceof Closure) {
                $closure = $this->valid;
                return $closure($value);
            }

            return $this->valid;
        }

        public function isMajor(): bool
        {
            return $this->isMajor;
        }
    };
}

function createType(bool $validate = true, bool $strict = true, string $name = "type"): DataTypeInterface
{
    return new class($validate, $strict, $name) implements DataTypeInterface
    {
        public function __construct(
            private bool $validate,
            private bool $strict,
            private string $name,
        ) {
        }

        public function validate(mixed $value): bool
        {
            return $this->validate;
        }

        public function strictValidate(mixed $value): bool
        {
            return $this->strict;
        }

        public function getName(): string
        {
            return $this->name;
        }
    };
}
