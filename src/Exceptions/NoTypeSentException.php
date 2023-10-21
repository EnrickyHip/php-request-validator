<?php

namespace Enricky\RequestValidator\Exceptions;

use Exception;

/** Exception for invalid data type. Used when a sent DataType does not exists in the `DataType` enum. */
class NoTypeSentException extends Exception
{
}
