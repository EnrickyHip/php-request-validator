<?php

namespace Enricky\RequestValidator\Abstract;

interface ValidatorInterface
{
    /** @return string[] */
    public function getErrors(): array;
    public function validate(): bool;
}
