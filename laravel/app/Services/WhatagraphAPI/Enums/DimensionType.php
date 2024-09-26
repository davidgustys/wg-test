<?php

namespace App\Services\WhatagraphAPI\Enums;

enum DimensionType: string
{
    case STRING = 'string';
    case INTEGER = 'int';
    case TIME = 'time';
    case FLOAT = 'float';
    case DATE = 'date';
}
