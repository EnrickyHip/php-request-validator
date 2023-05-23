<?php

namespace Enricky\RequestValidator\Exceptions;

use Exception;

/** Exception for invalid enum. Used when a sent enum class is not a subclass of `BackedEnum`. */
class InvalidEnumException extends Exception
{
}
