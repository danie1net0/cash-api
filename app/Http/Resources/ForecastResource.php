<?php

namespace App\Http\Resources;

use App\DTOs\ForecastData;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin ForecastData
 */
class ForecastResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'city' => $this->city,
            'temperature' => $this->temperature,
            'min_temperature' => $this->minTemperature,
            'max_temperature' => $this->maxTemperature,
            'weather' => $this->weather,
            'wind' => $this->wind,
            'humidity' => $this->humidity,
            'rain' => $this->rain,
        ];
    }
}
