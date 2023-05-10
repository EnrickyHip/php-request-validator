<?php

declare(strict_types=1);

namespace Enricky\Enums;

enum DataType: string
{
    case STRING = "string";
    case INT = "int";
    case FLOAT = "float";
    case BOOL = "bool";
    case NUMERIC = "numeric";
}
