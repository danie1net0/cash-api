<?php

namespace App\Providers;

use App\Factories\WeatherServiceFactory;
use App\Services\WeatherServiceInterface;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
    }

    public function boot(): void
    {
        $this->app->bind(WeatherServiceInterface::class, fn () => new WeatherServiceFactory()->create());
    }
}
