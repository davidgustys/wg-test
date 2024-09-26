<?php

namespace App\Services\OpenWeatherAPI;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Carbon;

class OpenWeatherAPIService
{
    private string $apiKey;
    private string $dataUrl;
    private string $geoUrl;

    public function __construct(string $dataUrl, string $geoUrl, string $apiToken)
    {
        $this->apiKey = $apiToken;
        $this->dataUrl = $dataUrl;
        $this->geoUrl = $geoUrl;
    }

    public function getDaySummary(float $lat, float $lon, Carbon $date)
    {
        $url = $this->dataUrl . 'onecall/day_summary';

        return Http::retry(3, 100)
            ->get($url, [
                'lat' => $lat,
                'lon' => $lon,
                'units' => 'metric',
                'date' => $date->format('Y-m-d'),
                'appid' => $this->apiKey,
            ])->throw()->json();
    }

    public function geocodeLocation(string $location, int $limit = 1)
    {
        $url = $this->geoUrl . 'direct';

        $results = Http::retry(3, 100)
            ->get($url, [
                'q' => $location,
                'limit' => $limit,
                'appid' => $this->apiKey,
            ])->throw()->json();

        if (empty($results)) {
            throw new \Exception('No results found for the given location.');
        }

        return $limit === 1 ? $results[0] : $results;
    }
}
