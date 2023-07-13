<?php

namespace Enricky\RequestValidator\Abstract;

/** Interface to represent data types */
interface DataTypeInterface
{
    public function strictValidate(mixed $value): bool;
    public function validate(mixed $value): bool;
    public function getName(): string;
}
