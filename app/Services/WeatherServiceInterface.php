<?php

namespace App\Services;

use App\DTOs\ForecastData;
use App\Exceptions\Services\WeatherService\{CityNotFoundException, UnauthenticatedException};

interface WeatherServiceInterface
{
    /**
     * @throws UnauthenticatedException
     * @throws CityNotFoundException
     */
    public function getForecast(string $city, string $country): ForecastData;
}
