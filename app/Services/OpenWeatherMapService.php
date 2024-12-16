<?php

namespace App\Services;

use App\DTOs\ForecastData;
use App\Exceptions\Services\WeatherService\{CityNotFoundException, UnauthenticatedException};
use Illuminate\Support\Facades\Http;

class OpenWeatherMapService implements WeatherServiceInterface
{
    /**
     * @throws UnauthenticatedException
     * @throws CityNotFoundException
     */
    public function getForecast(string $city, string $country): ForecastData
    {
        $url = config('services.weather.openweather.base_url') . '/' . config('services.weather.openweather.weather_endpoint');

        $response = Http::get($url, [
            'q' => "{$city},{$country}",
            'appid' => config('services.weather.openweather.api_key'),
            'units' => 'metric',
            'lang' => 'pt_br',
        ]);

        if ($response->getStatusCode() === 401) {
            throw new UnauthenticatedException();
        }

        if ($response->getStatusCode() === 404) {
            throw new CityNotFoundException();
        }

        $response = $response->fluent();

        return new ForecastData(
            city: $response->get("name"),
            temperature: $response->get("main.temp"),
            minTemperature: $response->get("main.temp_min"),
            maxTemperature: $response->get("main.temp_max"),
            weather: $response->get("weather.0.description"),
            wind: $response->get("wind.speed") * 3.6,
            humidity: $response->get("main.humidity"),
            rain: $response->get("rain.1h") ?? 0
        );
    }
}
