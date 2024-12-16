<?php

namespace App\Http;

use App\Http\Controllers\Controller;
use App\Http\Requests\ForecastRequest;
use App\Http\Resources\ForecastResource;
use App\Services\WeatherServiceInterface;

class WeatherController extends Controller
{
    public function __construct(public readonly WeatherServiceInterface $service)
    {
    }

    public function getForecast(ForecastRequest $request): ForecastResource
    {
        $forecast = $this->service->getForecast(
            $request->get('city'),
            $request->get('country')
        );

        return new ForecastResource($forecast);
    }
}
