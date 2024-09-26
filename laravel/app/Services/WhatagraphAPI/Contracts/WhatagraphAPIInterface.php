<?php

namespace App\Services\WhatagraphAPI\Contracts;

use App\Services\WhatagraphAPI\Requests\MetricRequest;
use App\Services\WhatagraphAPI\Requests\DimensionRequest;
use App\Services\WhatagraphAPI\Requests\DataRequest;
use App\Services\WhatagraphAPI\Responses\MetricResponse;
use App\Services\WhatagraphAPI\Responses\DimensionResponse;
use App\Services\WhatagraphAPI\Responses\DataPointResponse;

interface WhatagraphApiInterface
{
    public function getAllMetrics(array $filters = [], string $sortDirection = 'asc', string $sortField = '', int $perPage = 15, int $page = 1): array;
    public function addMetric(MetricRequest $metric): MetricResponse;
    public function getMetricById(int $metricId): MetricResponse;
    public function updateMetricById(int $metricId, MetricRequest $metric): MetricResponse;
    public function deleteMetricById(int $metricId): void;

    public function getAllDimensions(array $filters = [], string $sortDirection = 'asc', string $sortField = '', int $perPage = 15, int $page = 1): array;
    public function addDimension(DimensionRequest $dimension): DimensionResponse;
    public function getDimensionById(int $dimensionId): DimensionResponse;
    public function updateDimensionById(int $dimensionId, DimensionRequest $dimension): DimensionResponse;
    public function deleteDimensionById(int $dimensionId): void;

    public function getAllDataPoints(array $filters = [], string $sortDirection = 'asc', string $sortField = '', int $perPage = 15, int $page = 1): array;
    public function addDataPoints(array $dataPoints): array;
    public function getDataPointById(string $dataPointId): DataPointResponse;
    public function updateDataPoint(string $dataPointId, DataRequest $dataPoint): DataPointResponse;
    public function deleteDataPoint(string $dataPointId): void;
}
