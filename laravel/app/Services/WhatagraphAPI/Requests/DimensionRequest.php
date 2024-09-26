<?php

namespace App\Services\WhatagraphAPI\Requests;

use App\Services\WhatagraphAPI\Enums\DimensionType;

readonly class DimensionRequest
{
    public function __construct(
        public string $name,
        public string $external_id,
        public DimensionType $type
    ) {}
}
