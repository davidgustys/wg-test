<?php

namespace App\Services\WhatagraphAPI\Responses;

use App\Services\WhatagraphAPI\Enums\DimensionType;


class DimensionResponse
{
    public function __construct(
        public string $name,
        public string $externalId,
        public DimensionType $type
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            name: $data['name'],
            externalId: $data['external_id'],
            type: DimensionType::from($data['type'])
        );
    }
}
