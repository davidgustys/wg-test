<?php

namespace App\Console\Commands;

use App\Services\WhatagraphAPI\Enums\AccumulatorType;
use App\Services\WhatagraphAPI\Enums\MetricType;
use Illuminate\Console\Command;
use App\Services\WhatagraphAPI\WhatagraphAPIService;
use App\Services\WhatagraphAPI\Requests\DimensionRequest;
use App\Services\WhatagraphAPI\Requests\MetricRequest;
use App\Services\WhatagraphAPI\Enums\DimensionType;
use Illuminate\Http\Client\RequestException;

class SetupWhatagraphDimensionAndMetric extends Command
{
    protected $signature = 'whatagraph:create-dimension-metric';
    protected $description = 'Create a dimension "metric" and a metric "temperature" in Whatagraph';

    private WhatagraphAPIService $whatagraphService;

    public function __construct(WhatagraphAPIService $whatagraphService)
    {
        parent::__construct();
        $this->whatagraphService = $whatagraphService;
    }

    public function handle()
    {
        $this->setupDimensions();
        $this->setupMetrics();

        $this->info('Setup completed successfully!');
        return 0;
    }

    private function setupDimensions()
    {
        $this->info('Setting up dimension "metric","location"...');
        try {
            $dimensionRequest = new DimensionRequest(
                name: 'Metric',
                external_id: 'metric',
                type: DimensionType::STRING
            );
            $dimension = $this->whatagraphService->addDimension($dimensionRequest);
            $this->info("Dimension 'metric' created successfully with ID: {$dimension->externalId}");
        } catch (RequestException $e) {
            if ($e->response->status() === 409) {
                $this->info("Dimension 'metric' already exists.");
            } else {
                $this->error("Failed to create dimension: {$e->getMessage()}");
            }
        } catch (\Exception $e) {
            $this->error("An error occurred while setting up dimension: {$e->getMessage()}");
        }

        try {
            $dimensionRequest = new DimensionRequest(
                name: 'Weather Location',
                external_id: 'weather_location',
                type: DimensionType::STRING
            );
            $dimension = $this->whatagraphService->addDimension($dimensionRequest);
            $this->info("Dimension 'location' created successfully with ID: {$dimension->externalId}");
        } catch (RequestException $e) {
            if ($e->response->status() === 409) {
                $this->info("Dimension 'location' already exists.");
            } else {
                $this->error("Failed to create dimension: {$e->getMessage()}");
            }
        } catch (\Exception $e) {
            $this->error("An error occurred while setting up dimension: {$e->getMessage()}");
        }
    }

    private function setupMetrics()
    {
        $this->info('Setting up metric "temperature"...');
        try {
            $metricRequest = new MetricRequest(
                name: 'Temperature',
                external_id: 'temperature',
                type: MetricType::FLOAT,
                accumulator: AccumulatorType::AVERAGE,
                negative_ratio: false
            );
            $metric = $this->whatagraphService->addMetric($metricRequest);
            $this->info("Metric 'temperature' created successfully with ID: {$metric->id}");
        } catch (RequestException $e) {
            if ($$e->response->status() === 409) {
                $this->info("Metric 'temperature' already exists.");
            } else {
                $this->error("Failed to create metric: {$e->getMessage()}");
            }
        } catch (\Exception $e) {
            $this->error("An error occurred while setting up metric: {$e->getMessage()}");
        }
    }
}
