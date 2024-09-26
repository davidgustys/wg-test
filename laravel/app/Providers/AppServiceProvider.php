<?php

namespace App\Providers;

use App\Services\WhatagraphAPI\Contracts\WhatagraphApiInterface;
use Illuminate\Support\ServiceProvider;

use App\Services\OpenWeatherAPI\OpenWeatherAPIService;
use App\Services\WhatagraphAPI\WhatagraphAPIService;


class AppServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton(OpenWeatherAPIService::class, function ($app) {
            return new OpenWeatherAPIService(
                config('services.openweathermap.data_endpoint'),
                config('services.openweathermap.geo_endpoint'),
                config('services.openweathermap.token')
            );
        });

        $this->app->singleton(WhatagraphAPIService::class, function ($app) {
            return new WhatagraphAPIService(
                config('services.whatagraph.endpoint'),
                config('services.whatagraph.token')
            );
        });

        // Bind the interface to the concrete implementation
        $this->app->bind(WhatagraphApiInterface::class, WhatagraphAPIService::class);
    }
}
