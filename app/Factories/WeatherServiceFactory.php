<?php

namespace App\Factories;

use App\Exceptions\Services\WeatherService\NoServiceAvailable;
use App\Services\{OpenWeatherMapService, WeatherApiService, WeatherServiceInterface};

class WeatherServiceFactory
{
    /**
     * @throws NoServiceAvailable
     */
    public function create(): WeatherServiceInterface
    {
        $weatherApi = config('services.weather.weatherapi');
        $openWeather = config('services.weather.openweather');

        return match (true) {
            $weatherApi['is_active'] && $weatherApi['api_key'] => new WeatherApiService(),
            $openWeather['is_active'] && $openWeather['api_key'] => new OpenWeatherMapService(),
            default => throw new NoServiceAvailable(),
        };
    }
}
