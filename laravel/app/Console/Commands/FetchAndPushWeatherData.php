<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Validator;

use App\Services\OpenWeatherAPI\OpenWeatherAPIService;
use App\Services\WhatagraphAPI\Contracts\WhatagraphApiInterface;

class FetchAndPushWeatherData extends Command
{
    protected $signature = 'weather:fetch-and-push {location} {from} {to} {--batch-size=10}';
    protected $description = 'Fetch weather data for a location and date range, then push to Whatagraph';

    public function __construct(
        private OpenWeatherAPIService $weatherService,
        private WhatagraphApiInterface $whatagraphService
    ) {
        parent::__construct();
    }

    public function handle()
    {
        $location = $this->argument('location');
        $from = $this->argument('from');
        $to = $this->argument('to');
        $batchSize = $this->option('batch-size');

        $batchSize = $this->option('batch-size');

        // Validate inputs
        $validator = Validator::make([
            'location' => $location,
            'from' => $from,
            'to' => $to,
            'batch_size' => $batchSize,
        ], [
            'location' => 'required|string',
            'from' => 'required|date_format:Y-m-d',
            'to' => 'required|date_format:Y-m-d|after_or_equal:from',
            'batch_size' => 'integer|min:1',
        ]);

        if ($validator->fails()) {
            $this->error('Invalid input:');
            foreach ($validator->errors()->all() as $error) {
                $this->error($error);
            }
            return 1;
        }

        // Geocode location
        try {
            $geoData = $this->weatherService->geocodeLocation($location);
            $lat = $geoData['lat'];
            $lon = $geoData['lon'];
        } catch (\Exception $e) {
            $this->error("Error geocoding location: " . $e->getMessage());
            return 1;
        }

        // Fetch and push data for each day
        $currentDate = Carbon::parse($from);
        $endDate = Carbon::parse($to);

        $this->info("Fetching and pushing weather data for {$location} from {$from} to {$to}");

        $progressBar = $this->output->createProgressBar($currentDate->diffInDays($endDate) + 1);

        $dataPoints = [];

        while ($currentDate <= $endDate) {
            try {
                $weatherData = $this->weatherService->getDaySummary($lat, $lon, $currentDate);

                // Calculate median temperature
                $minTemp = $weatherData['temperature']['min'] ?? null;
                $maxTemp = $weatherData['temperature']['max'] ?? null;

                if ($minTemp !== null && $maxTemp !== null) {
                    $medianTemperature = ($minTemp + $maxTemp) / 2;
                    $dataPoints[] = [
                        'date' => $currentDate->format('Y-m-d'),
                        'temperature' => $medianTemperature,
                        'weather_location' => $location,
                        'metric' => 'celsius',
                    ];

                    if (count($dataPoints) >= $batchSize || $currentDate->equalTo($endDate)) {
                        $this->whatagraphService->addDataPoints($dataPoints);
                        $dataPoints = [];
                    }
                } else {
                    $this->warn("No temperature data available for {$currentDate->format('Y-m-d')}");
                }
            } catch (\Exception $e) {
                $this->error("Error processing data for {$currentDate->format('Y-m-d')}: " . $e->getMessage());
            }

            $currentDate->addDay();
            $progressBar->advance();
        }

        $progressBar->finish();
        $this->newLine();
        $this->info('Weather data fetched and pushed successfully!');
    }
}
