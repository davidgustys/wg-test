<?php

namespace App\Services\WhatagraphAPI\Responses;

use App\Services\WhatagraphAPI\Enums\{MetricType, AccumulatorType};

class MetricResponse
{
    public function __construct(
        public int $id,
        public string $name,
        public string $externalId,
        public MetricType $type,
        public bool $negativeRatio,
        public array $options
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            id: $data['id'],
            name: $data['name'],
            externalId: $data['external_id'],
            type: MetricType::from($data['type']),
            negativeRatio: $data['negative_ratio'],
            options: $data['options']
        );
    }
}
