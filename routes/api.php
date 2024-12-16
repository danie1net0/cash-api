<?php

use App\Http\WeatherController;
use Illuminate\Support\Facades\Route;

Route::get('forecast', [WeatherController::class, 'getForecast'])->name('forecast.get');
