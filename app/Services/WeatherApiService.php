<?php

namespace App\Services;

use App\DTOs\ForecastData;
use App\Exceptions\Services\WeatherService\{CityNotFoundException, UnauthenticatedException};
use Illuminate\Support\Facades\Http;

class WeatherApiService implements WeatherServiceInterface
{
    /**
     * @throws UnauthenticatedException
     * @throws CityNotFoundException
     */
    public function getForecast(string $city, string $country): ForecastData
    {
        $url = config('services.weather.weatherapi.base_url') . '/' . config('services.weather.weatherapi.weather_endpoint');

        $response = Http::get($url, [
            'q' => $city,
            'key' => config('services.weather.weatherapi.api_key'),
            'lang' => 'pt',
            'days' => '1',
        ]);

        if ($response->getStatusCode() === 403) {
            throw new UnauthenticatedException();
        }

        if ($response->getStatusCode() === 400 && $response->json('error.code') === 1006) {
            throw new CityNotFoundException();
        }

        $response = $response->fluent();

        return new ForecastData(
            city: $response->get("location.name"),
            temperature: $response->get("current.temp_c"),
            minTemperature: $response->get("forecast.forecastday.0.day.mintemp_c"),
            maxTemperature: $response->get("forecast.forecastday.0.day.maxtemp_c"),
            weather: $response->get("current.condition.text"),
            wind: $response->get("current.wind_kph"),
            humidity: $response->get("current.humidity"),
            rain: $response->get("forecast.forecastday.0.day.daily_chance_of_rain")
        );
    }
}
