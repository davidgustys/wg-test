<?php

namespace App\Services\WhatagraphAPI\Requests;

readonly class DataRequest
{
    public function __construct(
        public array $data
    ) {}
}
