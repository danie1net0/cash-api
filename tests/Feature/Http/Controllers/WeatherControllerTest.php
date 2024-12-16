<?php

namespace Tests\Controllers;

use App\DTOs\ForecastData;
use App\Exceptions\Services\WeatherService\{CityNotFoundException, UnauthenticatedException};
use App\Services\WeatherServiceInterface;

use function Pest\Laravel\{getJson, withoutExceptionHandling};

uses()->group('controllers');

beforeEach(function (): void {
    $this->weatherService = mock(WeatherServiceInterface::class);
    $this->app->instance(WeatherServiceInterface::class, $this->weatherService);
});

test('deve retornar os dados meteorológicos formatados', function (string $city, string $country): void {
    $output = new ForecastData(
        city: $city,
        temperature: 25.6,
        minTemperature: 20.3,
        maxTemperature: 28.9,
        weather: 'céu limpo',
        wind: 12.6,
        humidity: 65,
        rain: 2.1
    );

    $this->weatherService
        ->expects()
        ->getForecast($city, $country)
        ->andReturn($output);

    withoutExceptionHandling();

    $input = [
        'city' => $city,
        'country' => $country,
    ];

    getJson(route('forecast.get', $input))
        ->assertOk()
        ->assertJsonPath('data', [
            'city' => $city,
            'temperature' => 25.6,
            'min_temperature' => 20.3,
            'max_temperature' => 28.9,
            'weather' => 'céu limpo',
            'wind' => 12.6,
            'humidity' => 65,
            'rain' => 2.1,
        ]);
})->with('locale');

test('deve retornar erro quando a cidade não for encontrada', function (string $city, string $country): void {
    $this->weatherService
        ->expects()
        ->getForecast($city, $country)
        ->andThrow(new CityNotFoundException(), code: 404);

    $input = [
        'city' => $city,
        'country' => $country,
    ];

    getJson(route('forecast.get', $input))
        ->assertNotFound()
        ->assertJson([
            'message' => new CityNotFoundException()->getMessage(),
        ]);
})->with('locale');

test('deve retornar erro quando houver falha na autenticação', function (string $city, string $country): void {
    $this->weatherService
        ->expects()
        ->getForecast($city, $country)
        ->andThrow(new UnauthenticatedException());

    $input = [
        'city' => $city,
        'country' => $country,
    ];

    getJson(route('forecast.get', $input))
        ->assertUnauthorized()
        ->assertJson([
            'message' => new UnauthenticatedException()->getMessage(),
        ]);
})->with('locale');

test('deve validar os campos obrigatórios', function (string $field, string $city, string $country): void {
    $input = [
        'city' => $city,
        'country' => $country,
    ];

    unset($input[$field]);

    getJson(route('forecast.get', $input))
        ->assertUnprocessable()
        ->assertJsonValidationErrors([$field]);
})->with([
    'faltando parâmetro city' => 'city',
    'faltando parâmetro country' => 'country',
], 'locale');

test('o país deve ter exatamente 2 caracteres', function (): void {
    $input = [
        'city' => 'Brasília',
        'country' => 'BRA',
    ];

    getJson(route('forecast.get', $input))
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['country']);
});

test('a cidade não pode ter mais que 100 caracteres', function (): void {
    $input = [
        'city' => str_repeat('a', 101),
        'country' => 'BR',
    ];

    getJson(route('forecast.get', $input))
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['city']);
});

dataset('locale', [
    'cidade e estado' => [
        'city' => fake()->city,
        'country' => fake()->countryCode,
    ],
]);
