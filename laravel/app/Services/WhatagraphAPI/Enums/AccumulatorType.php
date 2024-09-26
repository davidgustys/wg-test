<?php

namespace App\Services\WhatagraphAPI\Enums;

enum AccumulatorType: string
{
    case SUM = 'sum';
    case AVERAGE = 'average';
    case LAST = 'last';
}
