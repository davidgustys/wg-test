<?php

namespace App\Services\WhatagraphAPI;

use Exception;
use App\Services\WhatagraphAPI\Contracts\WhatagraphApiInterface;
use App\Services\WhatagraphAPI\Requests\MetricRequest;
use App\Services\WhatagraphAPI\Requests\DimensionRequest;
use App\Services\WhatagraphAPI\Requests\DataRequest;
use App\Services\WhatagraphAPI\Responses\MetricResponse;
use App\Services\WhatagraphAPI\Responses\DimensionResponse;
use App\Services\WhatagraphAPI\Responses\DataPointResponse;
use Illuminate\Support\Facades\Http;

class WhatagraphAPIService implements WhatagraphApiInterface
{
    private string $baseUrl;
    private string $apiToken;

    private int $rateLimitRemaining = 200;

    public function __construct(string $baseUrl, string $apiToken)
    {
        $this->baseUrl = $baseUrl;
        $this->apiToken = $apiToken;
    }

    private function request(string $method, string $endpoint, mixed $data = null)
    {
        if ($this->rateLimitRemaining === 1) {
            sleep(61);
        }

        $response = Http::retry(3, function (int $attempt, Exception $exception) { //TODO: configurable retry count
            return $attempt * 100;
        })
            ->withHeaders([
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
            ])
            ->withToken($this->apiToken)
            ->timeout(10) //TODO: configurable timeout
            ->$method("{$this->baseUrl}/{$endpoint}", $data)
            ->throw();

        // Check for X-RateLimit-Remaining header and update the class variable
        if ($response->hasHeader('X-RateLimit-Remaining')) {
            $this->rateLimitRemaining = (int) $response->header('X-RateLimit-Remaining');

            if ($this->rateLimitRemaining <= 1) {
                throw new Exception('Rate limit not reset after waiting. Please try again later.');
            }
        }

        return $response->json();
    }

    public function getAllMetrics(array $filters = [], string $sortDirection = 'asc', string $sortField = '', int $perPage = 15, int $page = 1): array
    {
        $params = [
            'filter' => json_encode($filters),
            'sort_direction' => $sortDirection,
            'sort_field' => $sortField,
            'per_page' => $perPage,
            'page' => $page,
        ];

        $response = $this->request('get', 'v1/integration-metrics', $params);
        return array_map(fn($item) => MetricResponse::fromArray($item), $response['data']);
    }

    public function addMetric(MetricRequest $request): MetricResponse
    {
        $response = $this->request('post', 'v1/integration-metrics', $request);
        return MetricResponse::fromArray($response['data']);
    }

    public function getMetricById(int $metricId): MetricResponse
    {
        $response = $this->request('get', "v1/integration-metrics/{$metricId}");
        return MetricResponse::fromArray($response['data']);
    }

    public function updateMetricById(int $metricId, MetricRequest $request): MetricResponse
    {
        $response = $this->request('put', "v1/integration-metrics/{$metricId}", $request);
        return MetricResponse::fromArray($response['data']);
    }

    public function deleteMetricById(int $metricId): void
    {
        $this->request('delete', "v1/integration-metrics/{$metricId}");
    }

    public function getAllDimensions(array $filters = [], string $sortDirection = 'asc', string $sortField = '', int $perPage = 15, int $page = 1): array
    {
        $params = [
            'filter' => json_encode($filters),
            'sort_direction' => $sortDirection,
            'sort_field' => $sortField,
            'per_page' => $perPage,
            'page' => $page,
        ];

        $response = $this->request('get', 'v1/integration-dimensions', $params);
        return array_map(fn($item) => DimensionResponse::fromArray($item), $response['data']);
    }

    public function addDimension(DimensionRequest $request): DimensionResponse
    {
        $response = $this->request('post', 'v1/integration-dimensions', $request);
        return DimensionResponse::fromArray($response['data']);
    }

    public function getDimensionById(int $dimensionId): DimensionResponse
    {
        $response = $this->request('get', "v1/integration-dimensions/{$dimensionId}");
        return DimensionResponse::fromArray($response['data']);
    }

    public function updateDimensionById(int $dimensionId, DimensionRequest $request): DimensionResponse
    {
        $response = $this->request('put', "v1/integration-dimensions/{$dimensionId}", $request);
        return DimensionResponse::fromArray($response['data']);
    }

    public function deleteDimensionById(int $dimensionId): void
    {
        $this->request('delete', "v1/integration-dimensions/{$dimensionId}");
    }

    public function getAllDataPoints(array $filters = [], string $sortDirection = 'asc', string $sortField = '', int $perPage = 15, int $page = 1): array
    {
        $params = [
            'filter' => json_encode($filters),
            'sort_direction' => $sortDirection,
            'sort_field' => $sortField,
            'per_page' => $perPage,
            'page' => $page,
        ];

        $response = $this->request('get', 'v1/integration-source-data', $params);
        return array_map(fn($item) => DataPointResponse::fromArray($item), $response['data']);
    }

    public function addDataPoints(array $dataPoints): array
    {
        $response = $this->request('post', 'v1/integration-source-data', ["data" => $dataPoints]);
        return array_map(fn($item) => DataPointResponse::fromArray($item), $response['data']);
    }

    public function getDataPointById(string $dataPointId): DataPointResponse
    {
        $response = $this->request('get', "v1/integration-source-data/{$dataPointId}");
        return DataPointResponse::fromArray($response['data']);
    }

    public function updateDataPoint(string $dataPointId, DataRequest $request): DataPointResponse
    {
        $response = $this->request('put', "v1/integration-source-data/{$dataPointId}", $request);
        return DataPointResponse::fromArray($response['data']);
    }

    public function deleteDataPoint(string $dataPointId): void
    {
        $this->request('delete', "v1/integration-source-data/{$dataPointId}");
    }
}
