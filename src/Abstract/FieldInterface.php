<?php

namespace Enricky\RequestValidator\Abstract;

/** Interface to represent request fields. It has a name (key) and value pair. */
interface FieldInterface
{
    /** @return string The field's name (key). */
    public function getName(): string;

    /** @return string The field's value. */
    public function getValue(): mixed;
}
