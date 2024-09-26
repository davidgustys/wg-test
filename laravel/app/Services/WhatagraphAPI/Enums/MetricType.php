<?php

namespace App\Services\WhatagraphAPI\Enums;

enum MetricType: string
{
    case INTEGER = 'int';
    case FLOAT = 'float';
    case CURRENCY = 'currency';
}
