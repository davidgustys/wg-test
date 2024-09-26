<?php

namespace Tests\Unit;

use App\Services\OpenWeatherAPI\OpenWeatherAPIService;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class OpenWeatherAPIServiceTest extends TestCase
{
    private OpenWeatherAPIService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new OpenWeatherAPIService(
            'https://api.openweathermap.org/data/3.0/',
            'https://api.openweathermap.org/geo/1.0/',
            'fake-api-key'
        );
    }

    public function testGetDaySummary()
    {
        Http::fake([
            '*api.openweathermap.org/data/3.0/onecall/day_summary*' => Http::response([
                'temperature' => 20,
                'humidity' => 50,
            ], 200)
        ]);

        $result = $this->service->getDaySummary(51.5074, -0.1278, Carbon::parse('2023-05-15'));

        $this->assertEquals(['temperature' => 20, 'humidity' => 50], $result);

        Http::assertSent(function ($request) {
            return str_contains($request->url(), 'api.openweathermap.org/data/3.0/onecall/day_summary') &&
                $request['lat'] == 51.5074 &&
                $request['lon'] == -0.1278 &&
                $request['date'] == '2023-05-15' &&
                $request['appid'] == 'fake-api-key';
        });

        // Debug information
        if (Http::recorded()->isEmpty()) {
            $this->fail('No HTTP requests were recorded.');
        }
    }

    public function testGeocodeLocation()
    {
        Http::fake([
            '*api.openweathermap.org/geo/1.0/direct*' => Http::response([
                ['name' => 'London', 'lat' => 51.5074, 'lon' => -0.1278, 'country' => 'GB']
            ], 200)
        ]);

        $result = $this->service->geocodeLocation('London');

        $this->assertEquals(
            ['name' => 'London', 'lat' => 51.5074, 'lon' => -0.1278, 'country' => 'GB'],
            $result
        );

        Http::assertSent(function ($request) {
            return str_contains($request->url(), 'api.openweathermap.org/geo/1.0/direct') &&
                $request['q'] == 'London' &&
                $request['limit'] == 1 &&
                $request['appid'] == 'fake-api-key';
        });

        // Debug information
        if (Http::recorded()->isEmpty()) {
            $this->fail('No HTTP requests were recorded.');
        }
    }

    public function testGeocodeLocationWithNoResults()
    {
        Http::fake([
            '*api.openweathermap.org/geo/1.0/direct*' => Http::response([], 200)
        ]);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('No results found for the given location.');

        $this->service->geocodeLocation('NonexistentPlace');
    }

    public function testGetDaySummaryWithApiError()
    {
        Http::fake([
            '*api.openweathermap.org/data/3.0/onecall/day_summary*' => Http::response(null, 500)
        ]);

        $this->expectException(\Illuminate\Http\Client\RequestException::class);

        $this->service->getDaySummary(51.5074, -0.1278, Carbon::parse('2023-05-15'));
    }
}
