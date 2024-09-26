<?php

namespace App\Services\WhatagraphAPI\Responses;

class DataPointResponse
{
    public function __construct(
        public string $id,
        public string $date,
        public array $integrationData
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            $data['id'],
            $data['date'],
            $data['integration_data']
        );
    }
}
