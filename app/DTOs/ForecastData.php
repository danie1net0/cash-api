<?php

namespace App\DTOs;

readonly class ForecastData
{
    public function __construct(
        public string $city,
        public float $temperature,
        public float $minTemperature,
        public float $maxTemperature,
        public string $weather,
        public float $wind,
        public float $humidity,
        public float $rain,
    ) {
    }
}
