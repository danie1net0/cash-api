<?php

use App\Exceptions\Services\WeatherService\NoServiceAvailable;
use App\Factories\WeatherServiceFactory;
use App\Services\{OpenWeatherMapService, WeatherApiService};

uses()->group('factories');

beforeEach(function (): void {
    config([
        'services.weather.weatherapi' => [
            'is_active' => false,
            'api_key' => null,
        ],
        'services.weather.openweather' => [
            'is_active' => false,
            'api_key' => null,
        ],
    ]);
});

test('deve criar instância do WeatherApiService quando weatherapi estiver ativo', function (): void {
    config([
        'services.weather.weatherapi.is_active' => true,
        'services.weather.weatherapi.api_key' => 'test-key',
    ]);

    $service = new WeatherServiceFactory()->create();

    expect($service)->toBeInstanceOf(WeatherApiService::class);
});

test('deve criar instância do OpenWeatherMapService quando openweather estiver ativo', function (): void {
    config([
        'services.weather.openweather.is_active' => true,
        'services.weather.openweather.api_key' => 'test-key',
    ]);

    $service = new WeatherServiceFactory()->create();

    expect($service)->toBeInstanceOf(OpenWeatherMapService::class);
});

test('deve lançar exceção quando nenhum serviço estiver ativo', function (): void {
    new WeatherServiceFactory()->create();
})->throws(NoServiceAvailable::class);

test('deve lançar exceção quando serviço estiver ativo mas sem api key', function (): void {
    config([
        'services.weather.weatherapi.is_active' => true,
        'services.weather.weatherapi.api_key' => null,
    ]);

    new WeatherServiceFactory()->create();
})->throws(NoServiceAvailable::class);

test('deve priorizar WeatherApi sobre OpenWeather quando ambos estiverem ativos', function (): void {
    config([
        'services.weather.weatherapi.is_active' => true,
        'services.weather.weatherapi.api_key' => 'weatherapi-key',
        'services.weather.openweather.is_active' => true,
        'services.weather.openweather.api_key' => 'openweather-key',
    ]);

    $service = new WeatherServiceFactory()->create();

    expect($service)->toBeInstanceOf(WeatherApiService::class);
});
