<?php

namespace Enricky\RequestValidator\Abstract;

/** Interface to represent request attributes. It has a name (key) and value pair. */
interface AttributeInterface
{
    /** @return string The attribute's name (key). */
    public function getName(): string;

    /** @return mixed The attribute's value. */
    public function getValue(): mixed;
}
