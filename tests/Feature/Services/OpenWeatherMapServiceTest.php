<?php

namespace Tests\Services;

use App\DTOs\ForecastData;
use App\Exceptions\Services\WeatherService\CityNotFoundException;
use App\Services\OpenWeatherMapService;
use Illuminate\Support\Facades\{Config, Http};
use Illuminate\Http\Client\Request;

uses()->group('services');

beforeEach(function (): void {
    Config::set('services.weather.openweather.base_url', $this->url = 'https://api.openweathermap.org/data/2.5');
    Config::set('services.weather.openweather.weather_endpoint', $this->endpoint = 'weather');
    Config::set('services.weather.openweather.api_key', $this->key = 'test-api-key');
});

test('deve retornar dados meteorológicos formatados corretamente', function (string $city, string $country): void {
    Http::fake([
        'api.openweathermap.org/*' => Http::response([
            'main' => [
                'temp' => 25.6,
                'temp_min' => 20.3,
                'temp_max' => 28.9,
                'humidity' => 65,
            ],
            'weather' => [
                ['description' => 'céu limpo'],
            ],
            'wind' => [
                'speed' => 3.5, // m/s
            ],
            'rain' => ['1h' => 2.1],
            'name' => $city,
        ]),
    ]);

    $output = new OpenWeatherMapService()->getForecast($city, $country);

    expect($output)
        ->toBeInstanceOf(ForecastData::class)
        ->city->toBe($city)
        ->temperature->toBe(25.6)
        ->minTemperature->toBe(20.3)
        ->maxTemperature->toBe(28.9)
        ->weather->toBe('céu limpo')
        ->wind->toBe(3.5 * 3.6) // Convertido para km/h
        ->humidity->toBe(65.0)
        ->rain->toBe(2.1);
})->with('locale');

test('deve tratar resposta sem chuva definindo valor zero', function (string $city, string $country): void {
    Http::fake([
        'api.openweathermap.org/*' => Http::response([
            'main' => [
                'temp' => 25.6,
                'temp_min' => 20.3,
                'temp_max' => 28.9,
                'humidity' => 65,
            ],
            'weather' => [
                ['description' => 'céu limpo'],
            ],
            'wind' => [
                'speed' => 3.5,
            ],
            // Sem o campo 'rain'
            'name' => $city,
        ]),
    ]);

    $output = new OpenWeatherMapService()->getForecast($city, $country);

    expect($output->rain)->toBe(0.0);
})->with('locale');

test('deve lançar exceção quando a API retorna erro', function (): void {
    Http::fake([
        'api.openweathermap.org/*' => Http::response([
            'cod' => '404',
            'message' => 'city not found',
        ], 404),
    ]);

    new OpenWeatherMapService()->getForecast('CidadeInexistente', 'XX');
})->throws(CityNotFoundException::class);

test('deve criar a requisição com os dados corretos', function (string $city, string $country): void {
    Http::fake([
        'api.openweathermap.org/*' => Http::response([
            'main' => [
                'temp' => 25.6,
                'temp_min' => 20.3,
                'temp_max' => 28.9,
                'humidity' => 65,
            ],
            'weather' => [
                ['description' => 'céu limpo'],
            ],
            'wind' => [
                'speed' => 3.5,
            ],
            'name' => $city,
        ]),
    ]);

    new OpenWeatherMapService()->getForecast($city, $country);

    Http::assertSent(
        fn (Request $request) => 'GET' === $request->method()
        && "{$this->url}/{$this->endpoint}" === explode('?', $request->url())[0]
        && [
            'q' => "{$city},{$country}",
            'appid' => $this->key,
            'units' => 'metric',
            'lang' => 'pt_br',
        ] === $request->data()
    );
})->with('locale');

dataset('locale', [
    'cidade e estado' => [
        'city' => fake()->city,
        'country' => fake()->countryCode,
    ],
]);
