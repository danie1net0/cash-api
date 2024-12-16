<?php

namespace Tests\Services;

use App\DTOs\ForecastData;
use App\Exceptions\Services\WeatherService\{CityNotFoundException, UnauthenticatedException};
use App\Services\WeatherApiService;
use Illuminate\Support\Facades\{Config, Http};
use Illuminate\Http\Client\Request;

uses()->group('services');

beforeEach(function (): void {
    Config::set('services.weather.weatherapi.base_url', $this->url = 'http://api.weatherapi.com/v1');
    Config::set('services.weather.weatherapi.weather_endpoint', $this->endpoint = 'weather');
    Config::set('services.weather.weatherapi.api_key', $this->key = 'test-api-key');
});

test('deve retornar dados meteorológicos formatados corretamente', function (string $city, string $country): void {
    Http::fake([
        'api.weatherapi.com/*' => Http::response([
            'location' => [
                'name' => $city,
            ],
            'current' => [
                'temp_c' => 25.6,
                'wind_kph' => 9.7,
                'humidity' => 65,
                'condition' => [
                    'text' => 'Possibilidade de chuva irregular',
                ],
            ],
            'forecast' => [
                'forecastday' => [[
                    'day' => [
                        'maxtemp_c' => 28.9,
                        'mintemp_c' => 20.3,
                        'daily_chance_of_rain' => 2.1,
                    ],
                ]],
            ],
        ]),
    ]);

    $output = new WeatherApiService()->getForecast($city, $country);

    expect($output)
        ->toBeInstanceOf(ForecastData::class)
        ->city->toBe($city)
        ->temperature->toBe(25.6)
        ->minTemperature->toBe(20.3)
        ->maxTemperature->toBe(28.9)
        ->weather->toBe('Possibilidade de chuva irregular')
        ->wind->toBe(9.7)
        ->humidity->toBe(65.0)
        ->rain->toBe(2.1);
})->with('locale');

test('deve lançar exceção quando a API não encontra a cidade', function (): void {
    Http::fake([
        'api.weatherapi.com/*' => Http::response([
            "error" => [
                "code" => 1006,
                "message" => "No matching location found.",
            ],
        ], 400),
    ]);

    new WeatherApiService()->getForecast('Cidade Inexistente', 'XX');
})->throws(CityNotFoundException::class);

test('deve lançar exceção quando a chave da API for inválida', function (string $city, string $country): void {
    Http::fake([
        'api.weatherapi.com/*' => Http::response([
            "error" => [
                "code" => 2008,
                "message" => "API key has been disabled.",
            ],
        ], 403),
    ]);

    new WeatherApiService()->getForecast($city, $country);
})->throws(UnauthenticatedException::class)->with('locale');

test('deve criar a requisição com os dados corretos', function (string $city, string $country): void {
    Http::fake([
        'api.weatherapi.com/*' => Http::response([
            'location' => [
                'name' => $city,
            ],
            'current' => [
                'temp_c' => 25.6,
                'wind_kph' => 9.7,
                'humidity' => 65,
                'condition' => [
                    'text' => 'Possibilidade de chuva irregular',
                ],
            ],
            'forecast' => [
                'forecastday' => [[
                    'day' => [
                        'maxtemp_c' => 28.9,
                        'mintemp_c' => 20.3,
                        'daily_chance_of_rain' => 2.1,
                    ],
                ]],
            ],
        ]),
    ]);

    new WeatherApiService()->getForecast($city, $country);

    Http::assertSent(
        fn (Request $request) => 'GET' === $request->method()
            && "{$this->url}/{$this->endpoint}" === explode('?', $request->url())[0]
            && [
                'q' => $city,
                'key' => $this->key,
                'lang' => 'pt',
                'days' => '1',
            ] === $request->data()
    );
})->with('locale');

dataset('locale', [
    'cidade e estado' => [
        'city' => fake()->city,
        'country' => fake()->countryCode,
    ],
]);
