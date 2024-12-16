<?php

namespace App\Providers;

use App\Services\{OpenWeatherMapService, WeatherServiceInterface};
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
    }

    public function boot(): void
    {
        $this->app->bind(WeatherServiceInterface::class, fn () => new OpenWeatherMapService());
    }
}
