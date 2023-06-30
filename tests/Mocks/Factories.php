<?php

use Enricky\RequestValidator\Abstract\ValidationRule;

function createRule(bool $valid, bool $isMajor = false): ValidationRule
{
    return new class($valid, $isMajor) extends ValidationRule
    {
        public function __construct(
            private bool $valid,
            private bool $isMajor,
        ) {
        }

        public function validate(mixed $value): bool
        {
            return $this->valid;
        }

        public function isMajor(): bool
        {
            return $this->isMajor;
        }
    };
}
