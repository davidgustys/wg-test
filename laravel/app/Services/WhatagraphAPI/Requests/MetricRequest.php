<?php

namespace App\Services\WhatagraphAPI\Requests;

use App\Services\WhatagraphAPI\Enums\{MetricType, AccumulatorType};

readonly class MetricRequest
{
    public function __construct(
        public string $name,
        public string $external_id,
        public MetricType $type,
        public AccumulatorType $accumulator,
        public bool $negative_ratio
    ) {}
}
