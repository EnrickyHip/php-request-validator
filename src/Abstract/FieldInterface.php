<?php

namespace Enricky\RequestValidator\Abstract;

interface FieldInterface
{
    public function getName(): string;
    public function getValue(): mixed;
}
