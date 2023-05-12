<?php

declare(strict_types=1);

namespace Enricky\Enums;

use Exception;

class InvalidDataTypeException extends Exception
{
}

enum DataType: string
{
    case STRING = "string";
    case INT = "int";
    case FLOAT = "float";
    case BOOL = "bool";
    case NUMERIC = "numeric";
}
